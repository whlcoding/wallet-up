<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Services\TransactionService;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function store(TransactionRequest $transactionRequest): JsonResponse
    {
        try {
            $user = Auth::user();
            $wallet = (new WalletService)->findWalletByUser($user, $transactionRequest->input('wallet_id'));

            // $dataTransaction = $transactionRequest->only()

            $transaction = (new TransactionService)->createTransaction($wallet, $transactionRequest->toArray());

            return response()->json([
                'message' => 'Transaction Created Successfully!',
                'data' => $transaction
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create transaction!',
                'error' => $e->getMessage()
            ], $e->getCode() ?? 500);
        }
    }
}
