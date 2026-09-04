<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'reminder_id',
        'title',
        'message',
        'is_read',
        'created_at',
        'updated_at',
    ];
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }
}
