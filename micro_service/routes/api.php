<?php

use App\Http\Controllers\OrderController;
use App\Http\Middleware\VerifyAccessToken;
use Illuminate\Support\Facades\Route;

Route::middleware([VerifyAccessToken::class])->group(function () {

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->middleware('can:view,order');

    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
        ->middleware('can:delete,order');
});
