<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Reminder extends Model
{
    protected $fillable = [
        'user_id',
        'uuid',
        'title',
        'description',
        'assignee_ids',
        'due_task',
        'difficulty',
        'priority_level',
        'status',
        'created_at',
        'updated_at',
      
    ];
    
    protected $casts = [
        'assignee_ids' => 'array',
        'due_task' => 'datetime',
    ];
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Reminder $reminder): void {
            $reminder->uuid ??= (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function invitations()
    {
        return $this->hasMany(TaskInvitation::class);
    }
}