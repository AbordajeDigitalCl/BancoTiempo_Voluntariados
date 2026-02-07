<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne:id,name,email', 'userTwo:id,name,email'])
            ->withCount([
                'messages as unread_count' => function ($q) use ($user) {
                    $q->whereNull('read_at')->where('sender_id', '!=', $user->id);
                }
            ])
            ->latest('updated_at')
            ->get()
            ->map(function ($c) use ($user) {
                $other = $c->otherUser($user->id);

                return [
                    'id' => $c->id,
                    'otherUser' => $other ? ['id' => $other->id, 'name' => $other->name] : null,
                    'unreadCount' => (int) $c->unread_count,
                ];
            });

        return Inertia::render('Chat', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => [],
        ]);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        // seguridad: solo participantes
        if (!in_array($user->id, [(int)$conversation->user_one_id, (int)$conversation->user_two_id], true)) {
            abort(403);
        }

        // marcar leídos los mensajes recibidos
        Message::query()
            ->where('conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['read_at' => now()]);

        $conversation->load(['userOne:id,name,email', 'userTwo:id,name,email']);

        $msgs = Message::query()
            ->where('conversation_id', $conversation->id)
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'senderId' => (int) $m->sender_id,
                'senderName' => $m->sender?->name,
                'time' => $m->created_at ? $m->created_at->format('d/m/Y H:i') : '',
            ]);

        // recargar lista para sidebar
        $conversations = Conversation::query()
            ->where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne:id,name,email', 'userTwo:id,name,email'])
            ->withCount([
                'messages as unread_count' => function ($q) use ($user) {
                    $q->whereNull('read_at')->where('sender_id', '!=', $user->id);
                }
            ])
            ->latest('updated_at')
            ->get()
            ->map(function ($c) use ($user) {
                $other = $c->otherUser($user->id);

                return [
                    'id' => $c->id,
                    'otherUser' => $other ? ['id' => $other->id, 'name' => $other->name] : null,
                    'unreadCount' => (int) $c->unread_count,
                ];
            });

        $other = $conversation->otherUser($user->id);

        return Inertia::render('Chat', [
            'conversations' => $conversations,
            'activeConversation' => [
                'id' => $conversation->id,
                'otherUser' => $other ? ['id' => $other->id, 'name' => $other->name] : null,
            ],
            'messages' => $msgs,
        ]);
    }

    /**
     * Abre o crea conversación con un usuario (desde una publicación).
     */
    public function start(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $auth = $request->user();
        $otherId = (int) $request->input('user_id');

        if ($otherId === (int) $auth->id) {
            return redirect()->route('chat.index');
        }

        $a = min((int)$auth->id, $otherId);
        $b = max((int)$auth->id, $otherId);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $a,
            'user_two_id' => $b,
        ]);

        return redirect()->route('chat.show', ['conversation' => $conversation->id]);
    }
}
