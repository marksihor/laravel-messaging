<?php

namespace MarksIhor\LaravelMessaging\Services;

use MarksIhor\LaravelMessaging\Models\{Chat, ChatMessage};

class MessagingService
{
    public function createMessage(array $data): ChatMessage
    {
        return ChatMessage::create($data);
    }

    public function sendToChat(Chat $chat, $user, array $data)
    {
        $chat->touch();

        $this->createMessage([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'text' => $data['text'] ?? ''
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'The message has been sent.'
        ]);
    }

    public function sendToUser($sender, $recipient, array $data)
    {
        $chat = Chat::whereHas('users', function ($query) use ($sender, $recipient) {
            $query->whereIn('id', [$sender->id, $recipient->id]);
        })->first();

        if (!$chat) {
            $chat = Chat::create();

            $chat->users()->sync([$sender->id, $recipient->id]);
        }

        return $this->sendToChat($chat, $sender, $data);
    }
}