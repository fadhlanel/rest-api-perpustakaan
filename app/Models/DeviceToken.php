<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'fcm_token',
        'platform',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function registerToken(?int $userId, string $fcmToken, string $platform = 'android'): self
    {
        return static::updateOrCreate(
            ['fcm_token' => $fcmToken],
            [
                'user_id' => $userId,
                'platform' => $platform,
                'last_used_at' => now(),
            ]
        );
    }

    public static function unregisterToken(string $fcmToken): bool
    {
        return static::where('fcm_token', $fcmToken)->delete() > 0;
    }
}
