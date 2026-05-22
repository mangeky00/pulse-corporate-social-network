<?php

namespace App\Models;

use App\Support\AvatarDefaults;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'position',
        'department',
        'phone',
        'avatar',
        'role',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'is_admin' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'avatar_url',
        'is_admin',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(GroupChat::class, 'group_members')
            ->using(GroupMember::class)
            ->withPivot(['id', 'role', 'last_read_at'])
            ->withTimestamps();
    }

    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function groupMessages(): HasMany
    {
        return $this->hasMany(GroupMessage::class, 'sender_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getAvatarUrlAttribute(): string
    {
        return asset('uploads/'.ltrim($this->avatar ?: AvatarDefaults::USER, '/'));
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }
}
