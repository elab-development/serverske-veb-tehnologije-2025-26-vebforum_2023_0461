<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicForumServiceController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticsController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
Route::get('/public/news', [PublicForumServiceController::class, 'news']);
Route::get('/public/weather', [PublicForumServiceController::class, 'weather']);
Route::get('topics/{topic}/posts', [TopicController::class, 'posts']);
Route::get('/topics', [TopicController::class, 'index']);
Route::get('/topics/filter', [TopicController::class, 'filter']);
Route::get('/topics/{topic}', [TopicController::class, 'show']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/filter', [PostController::class, 'filter']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/topics', [TopicController::class, 'store']);
    Route::match(['put', 'patch'], '/topics/{topic}', [TopicController::class, 'update']);
    Route::delete('/topics/{topic}', [TopicController::class, 'destroy']);

    Route::post('/posts', [PostController::class, 'store']);
    Route::match(['put', 'patch'], '/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::match(['put', 'patch'], '/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/admin/topics/export', [TopicController::class, 'export'])->middleware('role:admin');
    Route::get('/admin/posts/export', [PostController::class, 'export'])->middleware('role:admin');
});

Route::apiResource('topics', TopicController::class)->only(['index', 'show']);
Route::apiResource('posts', PostController::class)->only(['index', 'show']);
Route::get('/statistics/topics', [StatisticsController::class, 'topics']);