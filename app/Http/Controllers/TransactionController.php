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
        $transactions = $this->transactionService->getAllTransactionsByWallet($this->user, $walletID);

        return response()->json([
            'message' => 'Transactions fetched successfully!',
            'data' => $transactions
        ], 200);
    }

    public function find(int $walletID, int $transactionId): JsonResponse
    {
        $transaction = $this->transactionService->getTransactionByWallet($this->user, $walletID, $transactionId);

        return response()->json([
            'message' => 'Transaction fetched successfully!',
            'data' => $transaction
        ], 200);
    }

    public function store(TransactionRequest $transactionRequest): JsonResponse
    {
        $transaction = $this->transactionService->createTransaction(
            $transactionRequest->input('wallet_id'),
            $transactionRequest->toArray()
        );

        return response()->json([
            'message' => 'Transaction Created Successfully!',
            'data' => $transaction
        ], 201);
    }

    public function update(UpdateTransactionRequest $transactionRequest): JsonResponse
    {
        $transaction = $this->transactionService->updateTransaction(
            $this->user,
            $transactionRequest->input('wallet_id'),
            $transactionRequest->input('transaction_id'),
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
