<?php

namespace MarksIhor\LaravelMessaging\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $guarded = ['id'];
//    public function chat()
//    {
//        return $this->belongsTo('MarksIhor\LaravelMessaging\Models\Chat');
//    }
//
//    public function user()
//    {
//        return $this->belongsTo(config('auth.providers.users.model'));
//    }
}
