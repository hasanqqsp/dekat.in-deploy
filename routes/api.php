<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::post('/register', [ApiController::class, 'register']);

Route::post('/login', [ApiController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/profile', [ApiController::class, 'profile']);
    
    Route::post('/logout', [ApiController::class, 'logout']);
});