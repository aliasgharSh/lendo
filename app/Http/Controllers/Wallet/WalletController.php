<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Resources\WalletResource;
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
    public function show()
    {
        // نمایش اطلاعات کیف پول کاربر
    }

    public function deposit(DepositRequest $request)
    {
        $wallet = $this->walletService->deposit(
            $request->user(),
            $request->validated('amount')
        );

        return $this->successResponse(
            'Deposit successful.',
            new WalletResource($wallet)
        );
    }

    public function withdraw(Request $request)
    {
        // کاهش موجودی کیف پول
    }

    public function transactions()
    {
        // تاریخچه تراکنش‌های کیف پول
    }
}
