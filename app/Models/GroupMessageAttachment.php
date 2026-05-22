<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMessageAttachment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'group_message_id',
        'file_name',
        'file_path',
        'file_type',
    ];

    protected $appends = [
        'url',
    ];

    public function groupMessage(): BelongsTo
    {
        return $this->belongsTo(GroupMessage::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('uploads/'.$this->file_path);
    }
}
