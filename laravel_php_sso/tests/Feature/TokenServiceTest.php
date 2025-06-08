<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TokenService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    #[DataProvider('userDataProvider')]
    public function test_generate_access_token(array $userData): void
    {
        $tokenService = app(TokenService::class);

        $user = new User();
        $user->forceFill($userData);

        $token = $tokenService->createAccessToken($user);

        $this->assertIsString($token);
        $this->assertMatchesRegularExpression('/^[\w-]+\.[\w-]+\.[\w-]+$/', $token);

        $payload = JWTAuth::setToken($token)->getPayload();

        $this->assertEquals($userData['id'], $payload->get('user_id'));
        $this->assertEquals($userData['roles'] ?? ['user'], $payload->get('roles'));
        $this->assertTrue($payload->get('exp') > now()->timestamp);
    }

    #[DataProvider('userDataProvider')]
    public function test_generate_and_save_refresh_token(array $userData): void
    {
        $tokenService = app(TokenService::class);

        $user = new User();
        $user->forceFill($userData);

        $refreshToken = $tokenService->generateAndSaveRefreshToken($user);
        $this->assertTrue(Str::isUuid($refreshToken));

        $key = TokenService::REFRESH_TOKEN_KEY_PREFIX . $refreshToken;
        $this->assertTrue(Redis::exists($key) > 0);

        $storedUserId = Redis::get($key);
        $this->assertEquals($user->id, $storedUserId);

        $ttl = Redis::ttl($key);
        $this->assertGreaterThan(0, $ttl);
        $this->assertLessThanOrEqual(TokenService::REFRESH_TOKEN_TTL_SECONDS, $ttl);

        Redis::del($key);
    }

    public static function userDataProvider(): array
    {
        return [
            'Обычный пользователь' => [[
                'id' => 41,
                'email' => 'user@example.com',
                'roles' => ['user'],
            ]],
            'Администратор' => [[
                'id' => 42,
                'email' => 'admin@example.com',
                'roles' => ['admin'],
            ]],
            'Без указания ролей' => [[
                'id' => 43,
                'email' => 'no-role@example.com',
                'roles' => null
            ]],
        ];
    }
}
