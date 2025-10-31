<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * WalletService
 */
class WalletService
{
    public function getAllWalletsByUser(User $user): Collection
    {
        return $user->wallets;
    }

    public function findWalletByUser(User $user, Int $wallet_id): Wallet
    {
        $wallet = $user->wallets()->where('wallets.id', $wallet_id)->first();
        if (!$wallet) {
            throw new ApiException("This Wallet doesn't exist", 404);
        }
        return $wallet;
    }

    public function createFirstWallet(User $user): Wallet
    {
        $fields = [
            'name' => 'My Wallet',
            'balance' => 0
        ];

        return $this->createWallet($user, $fields);
    }

    public function createWallet(User $user, array $fields): Wallet
    {
        return $user->wallets()->create($fields);
    }

    public function deleteWallet(User $user, Int $wallet_id): Bool
    {
        $wallet = $user->wallets()->where('wallets.id', $wallet_id)->first();
        if (!$wallet) {
            throw new Exception("This Wallet doesn't exist", 404);
        }
        return $wallet->delete();
    }
}
