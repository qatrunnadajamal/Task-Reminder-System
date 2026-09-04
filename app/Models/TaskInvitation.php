<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TaskInvitation extends Model
{
    protected $fillable = [
        'reminder_id',
        'user_id',
        'email',
        'token',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];
    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
