<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->invoice->balance / 100, 2);
        $dueDate = $this->invoice->due_date->format('F j, Y');

        return (new MailMessage)
            ->subject('Invoice Payment Reminder - ZIFA Connect')
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that invoice {$this->invoice->invoice_number} is due for payment.")
            ->line("Amount Due: {$this->invoice->currency} {$amount}")
            ->line("Due Date: {$dueDate}")
            ->action('Pay Now', url("/invoices/{$this->invoice->id}/pay"))
            ->line('Please make payment before the due date to avoid any service interruption.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_due_reminder',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->balance,
            'due_date' => $this->invoice->due_date->toISOString(),
            'message' => "Invoice {$this->invoice->invoice_number} is due for payment",
        ];
    }
}
