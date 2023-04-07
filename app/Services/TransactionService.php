<?php

namespace App\Services;

use App\Exceptions\ApiException;
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

    public function getTransactionByWallet(User $user, int $walletID, int $transactionID): Transaction
    {
        $wallet = $this->walletService->findWalletByUser($user, $walletID);

        $transaction = $wallet->transactions()->find($transactionID);

        if (!$transaction) {
            throw new ApiException('Transaction not found!', 404);
        }

        return $transaction;
    }


    public function updateTransaction(User $user, int $walletID, int $transactionID, array $args): bool
    {
        $wallet = $this->walletService->findWalletByUser($user, $walletID);

        $transaction = $wallet->transactions()->find($transactionID);

        if (!$transaction) {
            throw new ApiException('Transaction not found!', 404);
        }

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

        return $this->createTransaction($wallet, $recurringTransaction->wallet_id, $data);
    }
}

