<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_deposit_money(): void
    {
        //Arrange
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);
        // Act
        $response = $this->actingAs($user)->postJson("api/wallet/deposit", [
            'amount' => 50000,
        ]);
        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 150000,
        ]);
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 1000,
            'before_balance' => 5000,
            'after_balance' => 6000,
        ]);
    }
}
