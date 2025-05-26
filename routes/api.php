<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\LocationController;

Route::post('/register', [ApiController::class, 'register']);

Route::post('/login', [ApiController::class, 'login']);

## get the location for uhhhh idk whataever gibran want

Route::get('/categories', [LocationController::class, 'category']);   Route::post('/logout', [ApiController::class, 'logout']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::patch('/change-password', [ApiController::class, 'changePassword']);
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/locations', [LocationController::class, 'location']);
    Route::post('/locations/{location}/bookmark', [LocationController::class, 'bookmark']);
    Route::get('/bookmarks', [LocationController::class, 'getBookmarks']);
    Route::get('/locations/{location}/rating', [LocationController::class, 'getRating']);
});