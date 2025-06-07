<?php

namespace App\Http\Middleware;

use App\Http\Services\JwtTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessToken
{
    public function __construct(protected readonly JwtTokenService $jwtService)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $tokenString = $request->bearerToken();

        if (!$tokenString) {
            return response()->json([
                'error' => 'Access token not provided',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtService->parse($tokenString);

        if (!$token) {
            return response()->json([
                'error' => 'Invalid or expired token',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $claims = $token->claims();

        $request->merge([
            'user_id' => $claims->get('user_id'),
            'roles' => $claims->get('roles') ?? [],
        ]);

        return $next($request);
    }
}
