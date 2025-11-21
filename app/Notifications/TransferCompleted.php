<?php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Transfer $transfer
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $player = $this->transfer->player;
        $toClub = $this->transfer->toClub;

        return (new MailMessage)
            ->subject('Transfer Completed - ZIFA Connect')
            ->greeting("Hello {$notifiable->name},")
            ->line("The transfer for {$player->first_name} {$player->last_name} has been completed.")
            ->line("New Club: {$toClub->name}")
            ->line("Transfer Certificate: {$this->transfer->transfer_certificate_number}")
            ->action('View Transfer Details', url("/transfers/{$this->transfer->id}"))
            ->line('Thank you for using ZIFA Connect!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transfer_completed',
            'transfer_id' => $this->transfer->id,
            'player_name' => "{$this->transfer->player->first_name} {$this->transfer->player->last_name}",
            'to_club' => $this->transfer->toClub->name,
            'certificate' => $this->transfer->transfer_certificate_number,
            'message' => "Transfer completed for {$this->transfer->player->first_name} {$this->transfer->player->last_name}",
        ];
    }
}
