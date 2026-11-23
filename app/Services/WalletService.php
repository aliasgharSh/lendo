<?php

namespace App\Services;

use App\Models\User;
use RuntimeException;
use App\Exceptions\IdempotencyConflictException;
use App\Repositories\WalletRepository;

class WalletService
{
    public function __construct(
        private WalletRepository $walletRepository
    ) {}
    public function deposit(User $user, int $amount, string $key)
    {
        if ($amount <= 0) {
            throw new RuntimeException("Amount must be greater than zero");
        }

        return $this->walletRepository->transaction(function () use ($key, $user, $amount) {

            $wallet = $this->walletRepository->firstOrCreateForUser($user);

            $wallet = $this->walletRepository->lockForUpdateWallet($wallet);

            $existing = $this->walletRepository->findTransactionByKey($wallet, $key);

            if ($existing) {
                if ($existing->amount !== $amount || $existing->type !== 'deposit') {
                    throw new IdempotencyConflictException();
                }

                return $wallet;
            }

            $beforeBalance = $wallet->balance;
            $afterBalance = $beforeBalance + $amount;

            $this->walletRepository->updateBalance($wallet, $afterBalance);

            $this->walletRepository->createTransaction($wallet, "deposit", $amount, $beforeBalance, $afterBalance, $key);

            return $wallet;
        });
    }

    public function withdraw(User $user, int $amount, string $key)
    {
        if ($amount <= 0) {
            throw new RuntimeException('Amount must be greater than zero.');
        }
        return $this->walletRepository->transaction(function () use ($user, $amount, $key) {
            $wallet = $this->walletRepository->findForUserLocked($user);
            $existing = $this->walletRepository->findTransactionByKey($wallet, $key);

            if ($existing) {
                if ($existing->amount !== $amount || $existing->type !== 'withdrawal') {
                    throw new IdempotencyConflictException();
                }
                return $wallet;
            }

            $beforeBalance = $wallet->balance;

            if ($amount > $beforeBalance) {
                throw new RuntimeException('Insufficient balance.');
            }

            $afterBalance = $beforeBalance - $amount;
            $this->walletRepository->updateBalance($wallet, $afterBalance);
            $this->walletRepository->createTransaction($wallet, "withdrawal", $amount, $beforeBalance, $afterBalance, $key);
            return $wallet;
        });
    }
}
