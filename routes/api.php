<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\LocationController;

Route::post('/register', [ApiController::class, 'register']);

Route::post('/login', [ApiController::class, 'login']);

## get the location for uhhhh idk whataever gibran want
Route::get('/locations', [LocationController::class, 'location']);

Route::get('/categories', [LocationController::class, 'category']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/profile', [ApiController::class, 'profile']);
    
    Route::post('/logout', [ApiController::class, 'logout']);
});