<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $messageNotifications = [];
        $unreadMessageCount = 0;

        if ($user) {
            $all = $user->notifications()->latest()->take(15)->get();

            $onlyMessages = $all->filter(function ($n) {
                $type = (string) ($n->type ?? '');
                return str_contains($type, 'NewMessageNotification') || str_contains($type, 'Message');
            })->values();

            $messageNotifications = $onlyMessages->map(function ($n) {
                $data = (array) ($n->data ?? []);

                return [
                    'id' => $n->id,
                    'title' => $data['title'] ?? 'Mensaje',
                    'body' => $data['body'] ?? 'Tienes un nuevo mensaje.',
                    'time' => $n->created_at ? $n->created_at->diffForHumans() : '',
                    'unread' => $n->read_at === null,
                    'conversation_id' => $data['conversation_id'] ?? null,
                ];
            });

            $unreadMessageCount = $onlyMessages->whereNull('read_at')->count();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'messageNotifications' => $messageNotifications,
            'unreadMessageCount' => $unreadMessageCount,

            'nav' => [
                'badges' => [
                    'messages' => $unreadMessageCount,
                    'notifications' => 0,
                ],
            ],
        ];
    }
}
