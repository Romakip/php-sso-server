<?php

namespace App\Http\Controllers;

use App\Services\TokenService;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    public function redirectToGoogle(): Response
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(TokenService $tokenService): Response
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => Hash::make(Str::random())
            ]
        );

        $accessToken = $tokenService->createAccessToken($user);
        $refreshToken = $tokenService->generateAndSaveRefreshToken($user);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user,
        ]);
    }
}
