<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'instagram_id', 'instagram_username', 'profile_picture_url', 'bio', 'access_token', 'followers_count', 'media_count', 'connected_at', 'last_sync'])]
#[Hidden(['password', 'remember_token', 'access_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'connected_at' => 'datetime',
            'last_sync' => 'datetime',
            'followers_count' => 'integer',
            'media_count' => 'integer',
        ];
    }

    /**
     * Get the route key name for model binding via username.
     */
    public function getRouteKeyName(): string
    {
        return 'instagram_username';
    }
}
