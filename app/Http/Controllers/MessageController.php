<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if (!in_array($user->id, [(int)$conversation->user_one_id, (int)$conversation->user_two_id], true)) {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);

        // notificar al otro usuario
        /*
        $other = $conversation->otherUser($user->id);
        if ($other) {
            $other->notify(new NewMessageNotification([
                'title' => 'Mensaje',
                'body' => $user->name . ': ' . $data['body'],
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
            ]));
        }
        */

        return redirect()->route('chat.show', ['conversation' => $conversation->id]);
    }
}
