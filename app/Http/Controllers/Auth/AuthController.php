<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->create($data);
        return $this->successResponse("ثبت نام موفق",new UserResource($user),Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $result = $this->authService->login($data);
        if (! $result) {
            return $this->errorResponse("ایمیل یا رمز عبور اشتباه است", Response::HTTP_UNAUTHORIZED);
        }
        return $this->successResponse
        (
            "با موفقیت وارد شدید",
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ]
        );
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'با موفقیت از حساب خارج شدید',
        ]);
    }
}
