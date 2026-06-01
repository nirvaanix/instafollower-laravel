<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstagramAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instagram_id',
        'username',
        'profile_picture_url',
        'bio',
        'followers_count',
        'media_count',
        'access_token',
        'token_expires_at',
        'connected_at',
        'last_sync',
        'is_active',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected function casts(): array
    {
        return [
            'followers_count'  => 'integer',
            'media_count'      => 'integer',
            'token_expires_at' => 'datetime',
            'connected_at'     => 'datetime',
            'last_sync'        => 'datetime',
            'is_active'        => 'boolean',
        ];
    }

    /**
     * Route model binding by username for public profile URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    /**
     * The user who owns this Instagram account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Follower count history logs.
     */
    public function followerLogs(): HasMany
    {
        return $this->hasMany(FollowerLog::class);
    }

    /**
     * Check if the access token is expired or about to expire (within 7 days).
     */
    public function isTokenExpiringSoon(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        return $this->token_expires_at->subDays(7)->isPast();
    }

    /**
     * Check if the access token is fully expired.
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->isPast();
    }
}
