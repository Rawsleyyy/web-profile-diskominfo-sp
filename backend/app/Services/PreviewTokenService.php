<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Throwable;

class PreviewTokenService
{
    public static function make(int $userId, int $ttlMinutes = 30): string
    {
        return Crypt::encryptString(json_encode([
            'user_id' => $userId,
            'expires_at' => now()->addMinutes($ttlMinutes)->timestamp,
        ]));
    }

    public static function valid(?string $token): bool
    {
        if (! $token) return false;

        try {
            $data = json_decode(Crypt::decryptString($token), true, flags: JSON_THROW_ON_ERROR);
            return isset($data['expires_at']) && (int) $data['expires_at'] >= now()->timestamp;
        } catch (Throwable) {
            return false;
        }
    }
}
