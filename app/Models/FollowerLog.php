<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowerLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'instagram_account_id',
        'followers_count',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'followers_count' => 'integer',
            'recorded_at'     => 'datetime',
        ];
    }

    public function instagramAccount(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class);
    }
}
