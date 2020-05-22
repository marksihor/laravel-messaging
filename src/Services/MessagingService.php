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
        if (!isset($data['text']) || !$data['text']) return $this->errorResponse('Text is required');

        $chat->touch();

        $this->createMessage([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'text' => $data['text']
        ]);

        return [
            'status' => 'success',
            'message' => 'The message has been sent.'
        ];
    }

    public function sendToUser($sender, $recipient, array $data)
    {
        $chat = Chat::whereHas('users', fn($q) => $q->where('id', $recipient->id))->first();

        if (!$chat) {
            $chat = Chat::create();

            $chat->users()->sync([$sender->id, $recipient->id]);
        }

        return $this->sendToChat($chat, $sender, $data);
    }

    public static function errorResponse(string $message): array
    {
        return [
            'status' => 'error',
            'message' => $message
        ];
    }

    private static function markUnreadForRecipients(ChatMessage $message)
    {
        DB::table('chat_user')
            ->where('user_id', '<>', $message->user_id)
            ->where('chat_id', $message->chat_id)
            ->update(['read' => 0]);
    }

    public static function markReadForUser(int $chatId, int $userId, int $value)
    {
        DB::table('chat_user')
            ->where(['chat_id' => $chatId, 'user_id' => $userId])
            ->update(['read' => $value]);
    }
}