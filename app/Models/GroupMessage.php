<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_chat_id',
        'sender_id',
        'body',
    ];

    public function groupChat(): BelongsTo
    {
        return $this->belongsTo(GroupChat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(GroupMessageAttachment::class);
    }
}
