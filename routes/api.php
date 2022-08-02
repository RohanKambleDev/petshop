<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['api.format'])->prefix('v1/user/')->controller(AuthController::class)->group(function () {
    Route::post('create', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::get('logout', 'logout')->name('logout');
    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('reset-password-token', 'resetPasswordToken')->name('reset-password-token');
});

Route::middleware(['api.format', 'jwt.verify', 'auth', 'api'])->prefix('v1/user/')->controller(UserController::class)->group(function () {
    Route::get('/', 'view')->name('view-user-account');
    Route::delete('/', 'delete')->name('delete-user-account');
    Route::get('orders', 'delete')->name('list-user-orders');
    Route::put('edit', 'delete')->name('update-user');
});
