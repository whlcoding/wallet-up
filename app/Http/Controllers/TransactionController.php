<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionPaginationRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected User $user;

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }


    public function all(int $walletID): JsonResponse
    {
        $wallet = Wallet::with('transactions')->where('user_id', $this->user->id)->where('id',$walletID)->first();

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found!',
            ], 404);
        }

        $transactions = $wallet->transactions;

        return response()->json([
            'message' => 'Transactions fetched successfully!',
            'data' => $transactions
        ], 200);
    }

    public function find(int $walletID, int $transactionId): JsonResponse
    {

        $walletExists = Wallet::where('user_id', $this->user->id)->where('id',$walletID)->exists();

        if (!$walletExists) {
            return response()->json([
                'message' => 'Wallet not found!',
            ], 404);
        }

        $transaction = $this->transactionService->findTransaction($walletID, $transactionId);

        return response()->json([
            'message' => 'Transaction fetched successfully!',
            'data' => $transaction
        ], 200);
    }

    public function store(int $walletId, TransactionRequest $transactionRequest): JsonResponse
    {
        $wallet = Wallet::where('user_id', $this->user->id)->where('id',$walletId)->first();

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found!',
            ], 404);
        }

        $transaction = $wallet->transactions()->create($transactionRequest->toArray());

        return response()->json([
            'message' => 'Transaction Created Successfully!',
            'data' => $transaction
        ], 201);
    }

    public function update(int $walletID, int $transactionID, UpdateTransactionRequest $transactionRequest): JsonResponse
    {
        $walletExists = Wallet::where('user_id', $this->user->id)->where('id',$walletID)->exists();

        if (!$walletExists) {
            return response()->json([
                'message' => 'Wallet not found!',
            ], 404);
        }

        $transaction = $this->transactionService->updateTransaction(
            $walletID,
            $transactionID,
            $transactionRequest->toArray()
        );

        return response()->json([
            'message' => 'Transaction Updated Successfully!',
            'data' => $transaction
        ], );
    }

    public function pagination(int $walletID, TransactionPaginationRequest $transactionRequest): JsonResponse
    {
        $wallet = Wallet::where('user_id', $this->user->id)->where('id',$walletID)->first();

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found!',
            ], 404);
        }

        $transactions = TransactionService::paginatedTransactions($wallet, $transactionRequest);

        return response()->json([
            'message' => 'Transactions fetched successfully!',
            'data' => $transactions
        ], 200);
    }
}
