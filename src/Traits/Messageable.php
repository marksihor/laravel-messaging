<?php

namespace MarksIhor\LaravelMessaging\Traits;

use MarksIhor\LaravelMessaging\Models\Chat;
use MarksIhor\LaravelMessaging\Services\MessagingService;

trait Messageable
{
    public function chats()
    {
        return $this->belongsToMany('MarksIhor\LaravelMessaging\Models\Chat')
            ->orderBy('updated_at', 'desc')
            ->withPivot('read')
            ->with('message');
    }

    public function chat(int $id)
    {
        $chat = Chat::where('id', $id)->whereHas('users', function ($query) {
            $query->where('id', $this->id);
        })
            ->with('messages')
            ->first();

        if ($chat) MessagingService::markReadForUser($chat, $this->id);

        return $chat;
    }

    public function sendMessageToChat(int $chatId, array $data): array
    {
        $chat = $this->chat($chatId);

        if ($chat) {
            return (new MessagingService)->sendToChat($chat, $this, $data);
        }

        return MessagingService::errorResponse('No such chat.');

    }

    public function sendMessageToUser($user, array $data)
    {
        if ($this->id === $user->id) {
            return MessagingService::errorResponse('Trying to send the message to Yourself.');
        }

        return (new MessagingService)->sendToUser($this, $user, $data);
    }
}