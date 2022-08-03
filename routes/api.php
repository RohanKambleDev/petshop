<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['api.request', 'api.response', 'api'])->prefix('v1/user/')->controller(AuthController::class)->group(function () {
    Route::post('create', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::middleware(['jwt.verify'])->get('logout', 'logout')->name('logout');
    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('reset-password-token', 'resetPasswordToken')->name('reset-password-token');
});

Route::middleware(['api.request', 'jwt.verify', 'api.response'])->prefix('v1/')->controller(UserController::class)->group(function () {
    Route::get('user', 'show')->name('view-user-account');
    Route::delete('user/{$uuid}', 'destroy')->name('delete-user-account'); //role based
    Route::get('user/orders', 'orders')->name('list-user-orders'); //need orders table
    Route::put('user/edit', 'update')->name('update-user');
});
