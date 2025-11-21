<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Payment $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->payment->amount_cents / 100, 2);
        $invoice = $this->payment->invoice;

        return (new MailMessage)
            ->subject('Payment Received - ZIFA Connect')
            ->greeting("Hello {$notifiable->name},")
            ->line("We have received your payment of {$this->payment->currency} {$amount}.")
            ->line("Payment Reference: {$this->payment->payment_reference}")
            ->line("Invoice: {$invoice->invoice_number}")
            ->action('View Payment Details', url("/invoices/{$invoice->id}"))
            ->line('Thank you for your payment!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_received',
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount_cents,
            'currency' => $this->payment->currency,
            'reference' => $this->payment->payment_reference,
            'message' => "Payment of {$this->payment->currency} " . number_format($this->payment->amount_cents / 100, 2) . " received",
        ];
    }
}
