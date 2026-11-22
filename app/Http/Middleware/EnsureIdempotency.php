<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    $key = $request->header('Idempotency-Key');

    if (! $key || ! Str::isUuid($key)) {
        return response()->json([
            'success' => false,
            'message' => 'A valid Idempotency-Key header (UUID) is required.',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    return $next($request);
}
}
