<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function all(): JsonResponse
    {
        $user = Auth::user();

        try {
            $wallets = $this->walletService->getAllWalletsByUser($user);

            return response()->json([
                'data' => $wallets
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to get wallets!',
                'error' => $e->getMessage()
            ], $e->getCode() ?? 500);
        }
    }

    public function find(int $wallet_id): JsonResponse
    {
        $user = Auth::user();

        try {
            $wallet = $this->walletService->findWalletByUser($user, $wallet_id);

            return response()->json([
                'data' => $wallet
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to find wallet!',
                'error' => $e->getMessage()
            ], $e->getCode() ?? 500);
        }
    }


    public function store(WalletRequest $walletRequest): JsonResponse
    {
        $user = Auth::user();

        try {
            $fields = $walletRequest->only(['name']);
            $wallet = $this->walletService->createWallet($user, $fields);

            return response()->json([
                'data' => $wallet,
                'message' => 'Wallet Created Successfully'
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create wallet!',
                'error' => $e->getMessage()
            ], $e->getCode() ?? 500);
        }
    }

    public function update(WalletRequest $walletRequest, Int $wallet_id): JsonResponse
    {
        $user = Auth::user();

        $fields = $walletRequest->only(['name']);
        $wallet = Wallet::find($wallet_id);

        if (!$wallet || $wallet->user_id !== $user->id) {
            return response()->json([
                'message' => "This Wallet doesn't exist"
            ], 404);
        }

        $wallet->name = $fields['name'];
        $wallet->save();

        return response()->json([
            'data' => $wallet,
            'message' => 'Wallet Updated Successfully'
        ], 200);
    }
    public function destroy(Int $wallet_id): JsonResponse
    {
        $user = Auth::user();

        try {
            $this->walletService->deleteWallet($user, $wallet_id);

            return response()->json([
                'id' => $wallet_id,
                'message' => 'Wallet Deleted Successfully'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete wallet!',
                'error' => $e->getMessage()
            ], $e->getCode() ?? 500);
        }
    }
}
