<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('userDataProviderRegister')]
    public function test_register(array $userData): void
    {
        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'access_token',
                'refresh_token'
            ]);

        $this->assertIsString($response['access_token']);
        $this->assertIsString($response['refresh_token']);
    }

    public static function userDataProviderRegister(): array
    {
        return [
            'Пользователь 1' => [[
                'name' => 'Roman',
                'email' => 'testroman@mail.com',
                'password' => 'password',
            ]],
            'Пользователь 2' => [[
                'name' => 'Administrator',
                'email' => 'testadmin@mail.com',
                'password' => 'password',
            ]],
            'Аользователь 3' => [[
                'name' => 'Test',
                'email' => 'test@mail.com',
                'password' => 'password',
            ]],
        ];
    }
}
