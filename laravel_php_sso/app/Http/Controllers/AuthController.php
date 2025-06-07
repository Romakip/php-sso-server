<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\RegisterUser;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        protected readonly TokenService $tokenService
    ){}

    public function register(RegisterUser $request): Response
    {
        $dataUser = $request->all();

        $user = User::create([
            'name' => $dataUser['name'],
            'email' => $dataUser['email'],
            'password' => Hash::make($dataUser['password']),
        ]);

        $accessToken = $this->tokenService->generateAccessToken($user);
        $refreshToken = $this->tokenService->generateAndSaveRefreshToken($user->id);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): Response
    {
        $credentials = $request->all();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $accessToken = $this->tokenService->generateAccessToken($user);
        $refreshToken = $this->tokenService->generateAndSaveRefreshToken($user->id);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function refresh(RefreshTokenRequest $request): Response
    {
        $refreshToken = $request->get('refresh_token');

        $userId = $this->tokenService->findUserIdByRefreshToken($refreshToken);

        if (!$userId) {
            return response()->json(['error' => 'Invalid or expired refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $accessToken = $this->tokenService->generateAccessToken($user);

        return response()->json([
            'access_token' => $accessToken,
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function logout(RefreshTokenRequest $request): Response
    {
        $refreshToken = $request->get('refresh_token');

        $isDeleted = $this->tokenService->deleteRefreshToken($refreshToken);

        if (!$isDeleted) {
            return response()->json(['error' => 'Refresh token not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Successfully logout'], Response::HTTP_OK);
    }
}


