<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\InvoiceDueReminder;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'invoices:send-reminders
                            {--days=3 : Number of days before due date to send reminder}
                            {--overdue : Include overdue invoices}';

    protected $description = 'Send payment reminders for upcoming and overdue invoices';

    public function handle(): int
    {
        $days = $this->option('days');
        $includeOverdue = $this->option('overdue');

        $this->info("Sending payment reminders...");

        // Get invoices due within specified days
        $query = Invoice::where('status', 'pending')
            ->whereDate('due_date', '<=', now()->addDays($days));

        if (!$includeOverdue) {
            $query->whereDate('due_date', '>=', now());
        }

        $invoices = $query->with(['club.createdBy', 'entity'])->get();

        $count = 0;
        foreach ($invoices as $invoice) {
            $user = $this->getNotifiableUser($invoice);

            if ($user) {
                $user->notify(new InvoiceDueReminder($invoice));
                $count++;

                $this->line("Sent reminder for invoice {$invoice->invoice_number}");
            }
        }

        $this->info("Sent {$count} payment reminders.");

        return Command::SUCCESS;
    }

    private function getNotifiableUser(Invoice $invoice): ?\App\Models\User
    {
        // Try to get user from club
        if ($invoice->club_id && $invoice->club) {
            return $invoice->club->createdBy;
        }

        // Try to get user from entity (e.g., player's creator)
        if ($invoice->entity_type === 'registration' && $invoice->entity) {
            $registration = $invoice->entity;
            if ($registration->entity_type === 'player') {
                $player = \App\Models\Player::find($registration->entity_id);
                return $player?->creator;
            }
        }

        return null;
    }
}
