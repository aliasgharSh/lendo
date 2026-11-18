<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Wallet\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
    });
});

Route::middleware("auth:sanctum")->prefix("wallet")->controller(WalletController::class)->group(function () {
    Route::get("/show","show");
    Route::post("/deposit","deposit");
    Route::post("/withdraw","withdraw");
    Route::get("/transactions","transactions");
});
