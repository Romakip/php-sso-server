<?php

use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);

Route::get('google/redirect', [OAuthController::class, 'redirectToGoogle']);
Route::get('google/callback', [OAuthController::class, 'handleGoogleCallback']);
