<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use RuntimeException;
use Tests\TestCase;

class WalletDepositTest extends TestCase
{
    //واریز مبلغ مثبت باعث افزایش موجودی کیف پول شود.
    public function test_user_can_deposit_money(): void
    {
        //Arrange
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);
        $service = app(WalletService::class);

        //act
        $service->deposit($user, 50000);

        //Assert
        $this->assertDatabaseHas("wallets", [
            'id' => $wallet->id,
            'balance' => 150000,
        ]);
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 50000,
        ]);
    }

    //در صورت نبودن کیف پول، برای کاربر ساخته شود.
    public function test_deposit_creates_wallet_if_not_exists(): void
    {
        //Arrange
        $user = User::factory()->create();
        $service = app(WalletService::class);

        //act
        $service->deposit($user, 50000);

        //Asserts
        $this->assertDatabaseHas("wallets", [
            'user_id' => $user->id,
            'balance' => 50000,
        ]);
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $user->wallet->id,
            'type' => 'deposit',
            'amount' => 50000,
            'before_balance' => 0,
            'after_balance' => 50000,
        ]);
    }

    //اگر کیف پول از قبل وجود داشته باشد، همان کیف پول به‌روزرسانی شود.
    public function test_deposit_updates_existing_wallet(): void
    {
        //Arrange
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);
        $service = app(WalletService::class);
        $walletCount = Wallet::count();

        //act
        $service->deposit($user, 60000);

        //Asserts
        $this->assertDatabaseHas("wallets", [
            'id' => $wallet->id,
            'user_id' => $user->id,
            'balance' => 160000,
        ]);
        $this->assertDatabaseCount('wallets', $walletCount);
    }

    //اگر مبلغ صفر باشه، RuntimeException پرتاب بشه.
    public function test_deposit_throws_exception_when_amount_is_zero(): void
    {
        //Arrange
        $user = User::factory()->create();
        $service = app(WalletService::class);

        //Assert
        $this->expectException(RuntimeException::class);

        //act
        $service->deposit($user, 0);
    }
    // اگر مبلغ منفی بود اکسپشن پرتاب بشه
    public function test_deposit_throws_exception_when_amount_is_negative(): void
    {
        //Arrange
        $user = User::factory()->create();
        $service = app(WalletService::class);

        //Assert
        $this->expectException(RuntimeException::class);

        //act
        $service->deposit($user, -500);
    }
}
