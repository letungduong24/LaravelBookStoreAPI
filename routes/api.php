<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

// User
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::patch('users/update', [UserController::class, 'update'])->middleware('auth:sanctum');

// Book
Route::apiResource('books', BookController::class);

// Order
Route::apiResource('orders', OrderController::class)->middleware('auth:sanctum');


// Auth
Route::post('auth/register', [UserController::class, 'store']);
Route::post('auth/login', [UserController::class, 'login']);
Route::post('auth/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Admin
// Users
Route::middleware(['auth:sanctum', IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('users', [UserController::class, 'index']);
});