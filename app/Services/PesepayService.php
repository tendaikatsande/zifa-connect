<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PesepayService
{
    private string $baseUrl;
    private string $integrationKey;
    private string $encryptionKey;

    public function __construct()
    {
        $this->baseUrl = config('pesepay.base_url');
        $this->integrationKey = config('pesepay.integration_key');
        $this->encryptionKey = config('pesepay.encryption_key');
    }

    public function initiatePayment(Payment $payment, string $description): array
    {
        $payload = [
            'amountDetails' => [
                'amount' => $payment->amount_cents / 100,
                'currencyCode' => $payment->currency,
            ],
            'reasonForPayment' => $description,
            'resultUrl' => config('pesepay.result_url'),
            'returnUrl' => config('pesepay.return_url') . '?reference=' . $payment->payment_reference,
        ];

        $response = Http::withHeaders([
            'Authorization' => $this->integrationKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/payments/initiate", $payload);

        if (!$response->successful()) {
            Log::error('PesePay initiation failed', [
                'payment_id' => $payment->id,
                'response' => $response->body(),
            ]);

            throw new \Exception('Payment initiation failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'referenceNumber' => $data['referenceNumber'],
            'redirectUrl' => $data['redirectUrl'],
            'pollUrl' => $data['pollUrl'] ?? null,
        ];
    }

    public function checkStatus(string $referenceNumber): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->integrationKey,
        ])->get("{$this->baseUrl}/payments/check-payment", [
            'referenceNumber' => $referenceNumber,
        ]);

        if (!$response->successful()) {
            Log::error('PesePay status check failed', [
                'reference' => $referenceNumber,
                'response' => $response->body(),
            ]);

            return ['paid' => false, 'status' => 'unknown'];
        }

        $data = $response->json();

        return [
            'paid' => $data['transactionStatus'] === 'SUCCESS',
            'status' => $data['transactionStatus'],
            'data' => $data,
        ];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Pesepay-Signature');
        $webhookSecret = config('pesepay.webhook_secret');

        if (!$signature || !$webhookSecret) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }

    public function handleWebhook(Payment $payment, array $payload): void
    {
        $status = $payload['transactionStatus'] ?? 'UNKNOWN';

        $payment->update([
            'callback_payload' => $payload,
        ]);

        if ($status === 'SUCCESS') {
            $this->handleSuccessfulPayment($payment, $payload);
        } elseif (in_array($status, ['FAILED', 'CANCELLED'])) {
            $payment->update(['status' => 'failed']);
        }
    }

    public function handleSuccessfulPayment(Payment $payment, array $data): void
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'gateway_transaction_id' => $data['transactionId'] ?? null,
            'reconciled_at' => now(),
        ]);

        // Update invoice
        $invoice = $payment->invoice;
        if ($invoice) {
            $totalPaid = $invoice->payments()->where('status', 'paid')->sum('amount_cents');

            if ($totalPaid >= $invoice->amount_cents) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                ]);

                // Trigger post-payment actions
                $this->handleInvoicePaid($invoice);
            } else {
                $invoice->update(['status' => 'partial']);
            }
        }
    }

    private function handleInvoicePaid(Invoice $invoice): void
    {
        switch ($invoice->entity_type) {
            case 'registration':
                $registration = \App\Models\Registration::find($invoice->entity_id);
                if ($registration) {
                    $registration->update(['status' => 'pending_review']);

                    // Update player/entity status
                    if ($registration->entity_type === 'player') {
                        \App\Models\Player::where('id', $registration->entity_id)
                            ->update(['status' => 'under_review']);
                    }
                }
                break;

            case 'affiliation':
                $affiliation = \App\Models\Affiliation::find($invoice->entity_id);
                if ($affiliation) {
                    $affiliation->update([
                        'status' => 'active',
                        'payment_status' => 'paid',
                    ]);

                    // Activate club
                    \App\Models\Club::where('id', $affiliation->club_id)
                        ->update([
                            'status' => 'active',
                            'affiliation_expiry' => $affiliation->expiry_date,
                        ]);
                }
                break;

            case 'transfer':
                $transfer = \App\Models\Transfer::find($invoice->entity_id);
                if ($transfer && $transfer->status === 'pending_payment') {
                    $transfer->update(['status' => 'pending_zifa_review']);
                }
                break;
        }
    }
}
