<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use RuntimeException;
use Tests\TestCase;

class WalletWithdrawTest extends TestCase
{
    // 1. برداشت مبلغ مثبت، موجودی را کاهش دهد.
    public function test_user_can_withdraw_money(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);

        $service = app(WalletService::class);

        $service->withdraw($user, 40000);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 60000,
        ]);
    }

    // 2. برداشت باید یک تراکنش ثبت کند.
    public function test_withdraw_creates_transaction(): void
    {
        //Arrage
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);
        $service = app(WalletService::class);

        //Act
        $service->withdraw($user, 40000);

        //Assert
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'withdrawal',
            'amount' => 40000,
            'before_balance' => 100000,
            'after_balance' => 60000,
        ]);
    }

    // 3. برداشت بیشتر از موجودی مجاز نباشد.
    public function test_user_cannot_withdraw_more_than_balance(): void
    {
        //Arrange
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Insufficient balance.');

        app(WalletService::class)->withdraw($user, 150000);
    }

    // 4. مبلغ صفر پذیرفته نشود.
    public function test_withdraw_rejects_zero_amount(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Amount must be greater than zero.'
        );

        $user = User::factory()->create();

        app(WalletService::class)->withdraw($user, 0);
    }

    // 5. مبلغ منفی پذیرفته نشود.
    public function test_withdraw_rejects_negative_amount(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Amount must be greater than zero.'
        );

        $user = User::factory()->create();

        app(WalletService::class)->withdraw($user, -50000);
    }

    // 6. اگر کیف پول وجود نداشته باشد، برداشت انجام نشود.
    public function test_withdraw_fails_when_wallet_does_not_exist(): void
    {
        $user = User::factory()->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        app(WalletService::class)->withdraw($user, 50000);
    }
}
