<?php

namespace MarksIhor\LaravelMessaging\Traits;

use MarksIhor\LaravelMessaging\Models\Chat;
use MarksIhor\LaravelMessaging\Services\MessagingService;

trait Messageable
{
    public function chats()
    {
        return $this->belongsToMany('MarksIhor\LaravelMessaging\Models\Chat')
            ->orderBy('updated_at', 'asc')
            ->with('message');
    }

    public function chat(int $id)
    {
        return Chat::where('id', $id)->whereHas('users', function ($query) {
            $query->where('id', $this->id);
        })
            ->with('messages')
            ->first();
    }

    public function sendMessageToChat(int $chatId, array $data)
    {
        $chat = $this->chat($chatId);

        if ($chat) {
            return (new MessagingService)->sendToChat($chat, $this, $data);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No such chat.'
            ]);
        }
    }

    public function sendMessageToUser($user, array $data)
    {
        return (new MessagingService)->sendToUser($this, $user, $data);
    }
}