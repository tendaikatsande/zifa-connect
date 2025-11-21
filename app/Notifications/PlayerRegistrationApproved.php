<?php

namespace App\Notifications;

use App\Models\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayerRegistrationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Player $player
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Player Registration Approved - ZIFA Connect')
            ->greeting("Hello {$notifiable->name},")
            ->line("The player registration for {$this->player->first_name} {$this->player->last_name} has been approved.")
            ->line("ZIFA ID: {$this->player->zifa_id}")
            ->action('View Player Details', url("/players/{$this->player->id}"))
            ->line('Thank you for using ZIFA Connect!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'player_registration_approved',
            'player_id' => $this->player->id,
            'player_name' => "{$this->player->first_name} {$this->player->last_name}",
            'zifa_id' => $this->player->zifa_id,
            'message' => "Player {$this->player->first_name} {$this->player->last_name} registration approved",
        ];
    }
}
