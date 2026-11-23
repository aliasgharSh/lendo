<?php

namespace App\Repositories;

use App\Http\Requests\DepositRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletRepository
{
    public function findByUser(User $user): ?Wallet
    {
        return $user->wallet;
    }

    public function create(User $user): Wallet
    {
        return $user->wallet()->create([
            'balance' => 0,
        ]);
    }

    public function firstOrCreateForUser(User $user)
    {
        return $user->wallet()->firstOrCreate([], ['balance' => 0]);
    }

    public function lockForUpdateWallet(Wallet $wallet)
    {
        return Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
    }

    public function findTransactionByKey(Wallet $wallet, string $key)
    {
        return $wallet->transactions()->where('idempotency_key', $key)->first();
    }

    public function updateBalance(Wallet $wallet, int $balance)
    {
        return $wallet->update(['balance' => $balance,]);
    }

    public function createTransaction(Wallet $wallet, string $type, int $amount, int $beforeBalance, int $afterBalance, string $key)
    {
        return $wallet->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
            'idempotency_key' => $key,
        ]);
    }

    public function findForUserLocked(User $user): Wallet
    {
        return $user->wallet()->lockForUpdate()->firstOrFail();
    }

    public function transaction(callable $callback){
        return DB::transaction($callback);
    }
}
