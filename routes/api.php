<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopUpController;
use App\Http\Controllers\Api\WebHookController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\DataPlanController;
use App\Http\Controllers\Api\OperatorCardController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\TransferHistoriesController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;

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
Route::post('is-email-exist', [UserController::class, 'isEmailExist']);


Route::post('webhooks', [WebHookController::class, 'update']);

Route::group(['middleware' => 'jwt.verify'], function ($router) {
    Route::post('top_up', [TopUpController::class, 'store']);
    Route::post('transfers', [TransferController::class, 'store']);
    Route::post('data_plans', [DataPlanController::class, 'store']);
    Route::get('operator_cards', [OperatorCardController::class, 'index']);
    Route::get('payment_methods', [PaymentMethodController::class, 'index']);
    Route::get('transfer_histories', [TransferHistoriesController::class, 'index']);
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::get('users', [UserController::class, 'show']);
    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);
    Route::put('users', [UserController::class, 'update']);
    Route::get('wallets', [WalletController::class, 'show']);
    Route::put('wallets', [WalletController::class, 'update']);
});