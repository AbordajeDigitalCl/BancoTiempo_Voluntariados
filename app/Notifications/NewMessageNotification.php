<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $payload
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->payload['title'] ?? 'Nuevo mensaje',
            'body'  => $this->payload['body'] ?? 'Tienes un nuevo mensaje.',
            'conversation_id' => $this->payload['conversation_id'] ?? null,
            'sender_id' => $this->payload['sender_id'] ?? null,
        ];
    }
}
