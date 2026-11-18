<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{

    public function deposit(User $user, int $amount)
    {
        if ($amount <= 0) {
            throw new RuntimeException("Amount must be greater than zero");
        }

        return DB::transaction(function () use ($user, $amount) {

            $wallet = $user->wallet()->firstOrCreate(
                [],
                ['balance' => 0]
            );

            $wallet = Wallet::whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            $beforeBalance = $wallet->balance;
            $afterBalance = $beforeBalance + $amount;

            $wallet->update([
                'balance' => $afterBalance,
            ]);

            $wallet->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
            ]);

            return $wallet;
        });
    }




    public function withdraw(User $user, int $amount)
    {
        if ($amount <= 0) {
            throw new RuntimeException(
                'Amount must be greater than zero.'
            );
        }

        return DB::transaction(function () use ($user, $amount) {

            $wallet = $user->wallet()
                ->lockForUpdate()
                ->firstOrFail();

            $beforeBalance = $wallet->balance;

            if ($amount > $beforeBalance) {
                throw new RuntimeException('Insufficient balance.');
            }

            $afterBalance = $beforeBalance - $amount;

            $wallet->update([
                'balance' => $afterBalance,
            ]);

            $wallet->transactions()->create([
                'type' => 'withdrawal',
                'amount' => $amount,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
            ]);

            return $wallet;
        });
    }
}
