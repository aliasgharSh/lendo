<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Services\WalletService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;

class WalletController extends Controller
{
    use ApiResponse;
    public function __construct(
        private WalletService $walletService,

    ) {}
    // نمایش اطلاعات کیف پول کاربر
    public function show(Request $request)
    {
        $user = $request->user()->load('wallet');

        $wallet = $user->wallet;
        if (!$wallet) {
            return $this->errorResponse('Wallet not found.', 404);
        }
        return $this->successResponse('Wallet information.', new WalletResource($wallet));
    }

    public function deposit(DepositRequest $request)
    {
        $wallet = $this->walletService->deposit($request->user(), $request->validated('amount'));
        return $this->successResponse('Deposit successful.', new WalletResource($wallet));
    }

    public function withdraw(WithdrawRequest $request)
    {
        $wallet = $this->walletService->withdraw($request->user(), $request->validated('amount'));
        return $this->successResponse('Withdrawal successful.', new WalletResource($wallet));
    }

    public function transactions(Request $request)
    {
        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return $this->errorResponse('Wallet not found.', 404);
        }

        $transactions = $wallet->transactions;

        if ($transactions->isEmpty()) {
            return $this->errorResponse('No transactions found.', 404);
        }
        return $this->successResponse('Wallet transactions information.',WalletTransactionResource::collection($transactions));
    }
}
