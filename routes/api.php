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

Route::controller(AuthController::class)->group(function () {
    Route::post('v1/user/create', 'register')->name('register');
    Route::post('v1/user/login', 'login')->name('login');
    Route::get('v1/user/logout', 'logout')->name('logout');
    Route::post('v1/user/forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('v1/user/reset-password-token', 'resetPasswordToken')->name('reset-password-token');
});

Route::controller(UserController::class)->group(function () {
    Route::get('v1/user', 'view')->name('view-user-account');
    Route::delete('v1/user', 'delete')->name('delete-user-account');
    Route::get('v1/user/orders', 'delete')->name('list-user-orders');
    Route::put('v1/user/edit', 'delete')->name('update-user');
});
