<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PesepayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private PesepayService $pesepayService
    ) {}

    public function initiate(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Invoice is already paid'], 422);
        }

        $request->validate([
            'payment_method' => 'required|string|in:ecocash,onemoney,visa,mastercard,zipit',
        ]);

        try {
            $payment = DB::transaction(function () use ($invoice, $request) {
                // Create payment record
                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'payment_reference' => $this->generatePaymentReference(),
                    'amount_cents' => $invoice->balance,
                    'currency' => $invoice->currency,
                    'status' => 'initiated',
                    'gateway' => 'pesepay',
                    'gateway_method' => $request->payment_method,
                    'initiated_by' => $request->user()->id,
                    'initiated_at' => now(),
                ]);

                // Initiate PesePay transaction
                $pesepayResponse = $this->pesepayService->initiatePayment(
                    $payment,
                    $invoice->description
                );

                $payment->update([
                    'gateway_reference' => $pesepayResponse['referenceNumber'],
                    'gateway_response' => $pesepayResponse,
                    'status' => 'pending',
                ]);

                return $payment;
            });

            return response()->json([
                'payment_id' => $payment->id,
                'payment_url' => $payment->gateway_response['redirectUrl'] ?? null,
                'reference' => $payment->gateway_reference,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to initiate payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function status(Payment $payment): JsonResponse
    {
        // Check with PesePay if still pending
        if ($payment->isPending()) {
            $status = $this->pesepayService->checkStatus($payment->gateway_reference);

            if ($status['paid']) {
                $this->pesepayService->handleSuccessfulPayment($payment, $status);
            }
        }

        return response()->json([
            'status' => $payment->fresh()->status,
            'paid_at' => $payment->paid_at,
        ]);
    }

    public function webhook(Request $request): JsonResponse
    {
        // Verify webhook signature
        if (!$this->pesepayService->verifyWebhookSignature($request)) {
            Log::warning('Invalid PesePay webhook signature', [
                'payload' => $request->all()
            ]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $reference = $request->input('referenceNumber');
        $payment = Payment::where('gateway_reference', $reference)->first();

        if (!$payment) {
            Log::warning('Payment not found for webhook', ['reference' => $reference]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Idempotency check
        if ($payment->status === 'paid') {
            return response()->json(['message' => 'Already processed']);
        }

        try {
            DB::transaction(function () use ($payment, $request) {
                $this->pesepayService->handleWebhook($payment, $request->all());
            });

            return response()->json(['message' => 'Processed']);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Processing failed'], 500);
        }
    }

    private function generatePaymentReference(): string
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
}
