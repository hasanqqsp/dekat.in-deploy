<?php

use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BookmarkController;


Route::post('/register', [ApiController::class, 'register']);

Route::post('/login', [ApiController::class, 'login']);

Route::get('/categories', [LocationController::class, 'category']);
Route::post('/logout', [ApiController::class, 'logout']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('/update-password', [ApiController::class, 'changePassword']);
    Route::patch('/update-profile', [ApiController::class, 'updateProfile']);
    Route::get('/profile', [ApiController::class, 'profile']);
    // Route::post('/locations/delete', [LocationController::class, 'destroy']);
    Route::get('/locations/my', [LocationController::class, 'getMyLocations']);
    Route::post('/locations/add', [LocationController::class, 'store']);
    Route::delete('/locations/{id}', [LocationController::class, 'destroy']);
    Route::patch('/locations/{id}', [LocationController::class, 'update']);
    Route::get('/locations/{id}/reviews', [LocationController::class, 'getLocationReviews']);
    // Route::post('/locations/{location}/bookmark', [LocationController::class, 'bookmark']);
    // Route::get('/bookmarks', [LocationController::class, 'getBookmarks']);
    Route::apiResource('ratings', 'App\Http\Controllers\RatingController');
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks', [BookmarkController::class, 'destroy']);
    Route::get('/user/{user}/bookmarks', [BookmarkController::class, 'index']);
});
Route::get('/locations', [LocationController::class, 'index']);
