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

    public const EVENT_GENERATE_ACCESS_TOKEN = 'generate_access_token';
    public const EVENT_GENERATE_REFRESH_TOKEN = 'generate_refresh_token';

    public function __construct(
        protected readonly EventDispatcherInterface $eventDispatcher
    ){}

    public function createAccessToken(User $user): string
    {
        $accessToken = $this->generateAccessToken($user);
        $this->sendEventToDispatcher($user, self::EVENT_GENERATE_ACCESS_TOKEN);

        return $accessToken;
    }

    private function generateAccessToken(User $user): string
    {
        $payload = [
            'user_id' => $user->id,
            'roles' => $user->roles ?? ['user'],
            'exp' => now()->addMinutes(self::ACCESS_TOKEN_TTL_MINUTES)->timestamp
        ];

        return JWTAuth::claims($payload)->fromUser($user);
    }

    private function sendEventToDispatcher(User $user, string $event): void
    {
        $this->eventDispatcher->dispatch(self::STREAM, self::SUBJECT, $event, [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function generateAndSaveRefreshToken(User $user): string
    {
        $refreshToken = Str::uuid()->toString();
        Redis::setex(self::REFRESH_TOKEN_KEY_PREFIX . "{$refreshToken}", self::REFRESH_TOKEN_TTL_SECONDS, $user->id);
        $this->sendEventToDispatcher($user, self::EVENT_GENERATE_REFRESH_TOKEN);
        return $refreshToken;
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
