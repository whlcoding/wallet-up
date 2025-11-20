<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

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
Route::get('v1', [AuthController::class, 'index']);
Route::post('v1/login', [AuthController::class, 'login']);

Route::post('v1/signup', [AuthController::class, 'signup']);

//Route::get('v1/test', function () {
//    dd('opa');
//    return response()->json([
//        'message' => 'Hello World'
//    ]);
//});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('v1/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['api', 'auth:sanctum'])->prefix('v1')->group(function () {

    // WALLETS
    Route::get('/wallets', [WalletController::class, 'all']);
    Route::get('/wallets/{wallet_id}', [WalletController::class, 'find']);
    Route::post('/wallets', [WalletController::class, 'store']);
    Route::delete('/wallets/{wallet_id}', [WalletController::class, 'destroy']);

    // TRANSACTIONS
    Route::get('/wallets/{wallet_id}/transactions', [TransactionController::class, 'all']);
    Route::get('/wallets/{wallet_id}/transactions/{transaction_id}', [TransactionController::class, 'find']);
    Route::post('/wallets/{wallet_id}/transactions', [TransactionController::class, 'store']);
    Route::delete('/wallets/{wallet_id}/transactions/{transaction_id}', [TransactionController::class, 'destroy']);
    Route::put('/wallets/{wallet_id}/transactions/{transaction_id}', [TransactionController::class, 'update']);
    Route::post('/wallets/{wallet_id}/transactions/pagination', [TransactionController::class, 'pagination']);
//    Route::post('/wallets/{wallet_id}/transactions/{transaction_id}/duplicate', [TransactionController::class, 'duplicate']

});
