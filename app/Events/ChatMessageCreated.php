<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $messageId,
        public int $conversationId,
        public int $senderId,
        public int $receiverId,
        public string $preview,
        public bool $isImage,
        public string $createdAt
    ) {

    }

    public function broadcastOn(): array
    {
        return [
            //Canal personal del receptor
            new PrivateChannel("App.Model.User.{$this->receiverId}"),

            //Canal de conversación
            new PrivateChannel("App.Model.Conversation.{$this->conversationId}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.created';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id'=> $this->messageId,
            'conversation_id'=> $this->conversationId,
            'sender_id'=> $this->senderId,
            'receiver_id'=> $this->receiverId,
            'preview'=> $this->preview,
            'is_image'=> $this->isImage,
            'created_at'=> $this->createdAt,
        ];
    } 
}
