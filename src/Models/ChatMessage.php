<?php

namespace MarksIhor\LaravelMessaging\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['updated_at', 'user_id', 'chat_id'];

    protected $casts = [
        'data' => 'array'
    ];

    public function chat()
    {
        return $this->belongsTo('MarksIhor\LaravelMessaging\Models\Chat');
    }
}
