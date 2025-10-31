<?php

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

class CreateTransactionTest extends TestCase
{
    public function test_user_can_create_transaction(): void
    {
        // Arrange
        
        /**
         * @var Authenticatable|User $user
         */
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id
        ]);

        // Act

        $response = $this->actingAs($user)->postJson("/api/v1/wallets/{$wallet->id}/transactions", [
            'name' => 'Test Transaction',
            'description' => 'Test Description',
            'type' => 'expense',
            'amount' => 1000,
        ]);

        dd($wallet);
    }

}
