<?php

namespace App\Services;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;

class TransactionService
{

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

