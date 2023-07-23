<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopUpController;
use App\Http\Controllers\Api\WebHookController;
use App\Http\Controllers\Api\TransferController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('jwt.verify')->get('/test', function (Request $request) {
//     return 'success';
// });

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::post('webhooks', [WebHookController::class, 'update']);

Route::group(['middleware' => 'jwt.verify'], function ($router) {
    Route::post('top_up', [TopUpController::class, 'store']);
    Route::post('transfers', [TransferController::class, 'store']);
});