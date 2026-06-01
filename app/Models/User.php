<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * All Instagram accounts connected by this user.
     */
    public function instagramAccounts(): HasMany
    {
        return $this->hasMany(InstagramAccount::class);
    }

    /**
     * Only active Instagram accounts.
     */
    public function activeInstagramAccounts(): HasMany
    {
        return $this->hasMany(InstagramAccount::class)->where('is_active', true);
    }
}
