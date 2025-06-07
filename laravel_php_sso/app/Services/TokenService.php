<?php

namespace App\Services;

use App\Contracts\EventDispatcherInterface;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{
    public const ACCESS_TOKEN_TTL_MINUTES = 60;
    public const REFRESH_TOKEN_TTL_SECONDS = 604800;

    public const REFRESH_TOKEN_KEY_PREFIX = 'refresh_token';

    public const STREAM = 'backend';
    public const SUBJECT = 'backend.topic1';
    public const EVENT = 'generate_access_token';

    public function __construct(
        protected readonly EventDispatcherInterface $eventDispatcher
    ){}

    public function generateAndSaveRefreshToken(int $userId): string
    {
        $refreshToken = Str::uuid()->toString();

        Redis::setex(self::REFRESH_TOKEN_KEY_PREFIX . "{$refreshToken}", self::REFRESH_TOKEN_TTL_SECONDS, $userId);

        return $refreshToken;
    }

    public function generateAccessToken(User $user): string
    {
        $payload = [
            'user_id' => $user->id,
            'roles' => $user->roles ?? ['user'],
            'exp' => now()->addMinutes(self::ACCESS_TOKEN_TTL_MINUTES)->timestamp
        ];

        $this->eventDispatcher->dispatch(self::STREAM, self::SUBJECT, self::EVENT, [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return JWTAuth::claims($payload)->fromUser($user);
    }

    public function findUserIdByRefreshToken(string $refreshToken): ?string
    {
        return Redis::get(self::REFRESH_TOKEN_KEY_PREFIX . "{$refreshToken}");
    }

    public function deleteRefreshToken(string $refreshToken): bool
    {
        $key = self::REFRESH_TOKEN_KEY_PREFIX . "{$refreshToken}";

        if (!Redis::exists($key)) {
            return false;
        }

        Redis::del($key);

        return true;
    }
}
