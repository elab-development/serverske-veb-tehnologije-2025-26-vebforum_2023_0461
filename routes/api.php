<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiscussionStarterController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::get('categories/{category}/topics', [CategoryController::class, 'topics']);
Route::get('topics', [TopicController::class, 'index']);
Route::get('topics/export', [TopicController::class, 'export']);
Route::get('topics/{topic}', [TopicController::class, 'show']);
Route::get('topics/{topic}/posts', [TopicController::class, 'posts']);
Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{post}', [PostController::class, 'show']);
Route::get('search/topics', [TopicController::class, 'search']);
Route::get('statistics/topics', [TopicController::class, 'statistics']);
Route::get('discussion-starter', [DiscussionStarterController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return response()->json($request->user());
    });
    Route::post('user/avatar', [UserController::class, 'uploadAvatar']);

    Route::middleware('admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
        Route::get('users', [UserController::class, 'index']);
        Route::put('users/{user}/role', [UserController::class, 'updateRole']);
    });

    Route::post('topics', [TopicController::class, 'store']);
    Route::put('topics/{topic}', [TopicController::class, 'update']);
    Route::delete('topics/{topic}', [TopicController::class, 'destroy']);
    Route::post('topics/{topic}/vote', [TopicController::class, 'vote']);

    Route::get('posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('posts/{post}/comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

    Route::post('posts/{post}/like', [LikeController::class, 'togglePostLike']);
    Route::post('comments/{comment}/like', [LikeController::class, 'toggleCommentLike']);

    Route::post('posts', [PostController::class, 'store']);
    Route::put('posts/{post}', [PostController::class, 'update']);
    Route::delete('posts/{post}', [PostController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
