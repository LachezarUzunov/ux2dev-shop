<?php

namespace App\Http\Middleware;

use App\Services\IdempotencyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');
        if (! $key) {
            return response()->json(['error' => 'Idempotency Key Missing'],400);
        }

        return app(IdempotencyService::class)
            ->handle($request, $key, $next);
    }
}
