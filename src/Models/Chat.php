<?php

namespace MarksIhor\LaravelMessaging\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['pivot'];

    protected $appends = ['read'];

    public function messages()
    {
        return $this->hasMany('MarksIhor\LaravelMessaging\Models\ChatMessage')
            ->orderBy('id', 'desc');
    }

    public function message()
    {
        return $this->hasOne('MarksIhor\LaravelMessaging\Models\ChatMessage')
            ->orderBy('id', 'desc');
    }

    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'));
    }

    public function getReadAttribute()
    {
        return $this->pivot ? $this->pivot->read : null;
    }
}