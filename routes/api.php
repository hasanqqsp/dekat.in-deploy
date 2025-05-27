<?php

use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BookmarkController;


Route::post('/register', [ApiController::class, 'register']);

Route::post('/login', [ApiController::class, 'login']);

Route::get('/categories', [LocationController::class, 'category']);   Route::post('/logout', [ApiController::class, 'logout']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::patch('/update-password', [ApiController::class, 'changePassword']);
    Route::patch('/update-profile', [ApiController::class, 'updateProfile']);
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/locations', [LocationController::class, 'index']);
    Route::post('/locations/{ocation}/delete', [LocationController::class, 'destroy']);
    Route::post('/locations/add', [LocationController::class, 'store']);
    Route::patch('/locations/{location}/update', [LocationController::class, 'update']);
    // Route::post('/locations/{location}/bookmark', [LocationController::class, 'bookmark']);
    Route::get('/bookmarks', [LocationController::class, 'getBookmarks']);
    Route::get('/locations/{location}/reviews', [LocationController::class, 'getLocationReviews']);
    Route::apiResource('ratings', 'App\Http\Controllers\RatingController');
    Route::apiResource('bookmarks', BookmarkController::class)->only(['store','destroy']);
    Route::get('/user/{user}/bookmarks', [BookmarkController::class, 'index']);

});