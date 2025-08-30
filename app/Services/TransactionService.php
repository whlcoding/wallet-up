<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\TransactionPaginationRequest;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Collection;

class TransactionService
{
    protected WalletService $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService;
    }

    public function getAllTransactionsByWallet(User $user, int $walletID): Collection
    {
        $wallet = $this->walletService->findWalletByUser($user, $walletID);

        return $wallet->transactions;
    }


    public function findTransaction(int $walletID, int $transactionID): Transaction
    {
        $transaction = Transaction::where('wallet_id', $walletID)->find($transactionID);

        if (!$transaction) {
            throw new ApiException('Transaction not found!', 404);
        }

        return $transaction;
    }


    public function updateTransaction(int $walletID, int $transactionID, array $args): bool
    {
        $transaction = $this->findTransaction($walletID, $transactionID);

        return $transaction->update($args);
    }

    public function createTransaction(Wallet $wallet, array $args): Transaction
    {
        return $wallet->transactions()->create($args);
    }

    public function generateRecurringTransaction(Wallet $wallet, RecurringTransaction $recurringTransaction): Transaction
    {
        $data = [
            'name' => $recurringTransaction->name,
            'description' => $recurringTransaction->description,
            'type' => $recurringTransaction->type,
            'amount' => $recurringTransaction->amount,
            'due_date' => $recurringTransaction->due_date,
            'is_recurring' => true,
            'recurring_transaction_id' => $recurringTransaction->id
        ];

        return $this->createTransaction($wallet, $data);
    }


    public static function paginatedTransactions(Wallet $wallet, TransactionPaginationRequest $request): Collection
    {
        $transactions = $wallet->transactions()
            ->where('status', $request->status)
            ->where('type', $request->type)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        return $transactions;
    }
}
