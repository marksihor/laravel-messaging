<?php

namespace MarksIhor\LaravelMessaging\Services;

use Illuminate\Support\Facades\DB;
use MarksIhor\LaravelMessaging\Models\{Chat, ChatMessage};

class MessagingService
{
    public function createMessage(array $data): ChatMessage
    {
        $message = ChatMessage::create($data);

        $this->markUnreadForRecipients($message);

        return $message;
    }

    public function sendToChat(Chat $chat, $user, array $data)
    {
        if (!count($data)) return $this->response('error', 'The message should not be empty.');

        $chat->touch();

        $message = $this->createMessage([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'data' => $data
        ]);

        return $this->response('success', $message);
    }

    public function sendToUser($sender, $recipient, array $data)
    {
        $chat = Chat::whereHas('users', fn($q) => $q->where('id', $sender->id))
            ->whereHas('users', fn($q) => $q->where('id', $recipient->id))
            ->first();

        if (!$chat) {
            $chat = Chat::create();

            $chat->users()->sync([$sender->id, $recipient->id]);

            $this->markReadForUser($chat->id, $sender->id, 1);
        }

        return $this->sendToChat($chat, $sender, $data);
    }

    public static function response(string $status, $message): array
    {
        return [
            'status' => $status,
            'message' => $message
        ];
    }

    public static function markUnreadForRecipients(ChatMessage $message): array
    {
        DB::table('chat_user')
            ->where('user_id', '<>', $message->user_id)
            ->where('chat_id', $message->chat_id)
            ->update(['read' => 0]);

        return self::response('success', 'The read mark was changed.');
    }

    public static function markReadForUser(int $chatId, int $userId, int $value): array
    {
        DB::table('chat_user')
            ->where(['chat_id' => $chatId, 'user_id' => $userId])
            ->update(['read' => $value]);

        return self::response('success', 'The read mark was changed.');
    }
}