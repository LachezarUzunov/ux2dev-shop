<?php

namespace App\Http\Middleware;

use App\Enums\RateLimitsCapacityEnum;
use App\Enums\RefillRatesEnum;
use App\Services\RateLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GlobalRateLimitMiddleware
{
    public function __construct(private RateLimitService $rateLimiter) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $capacity = RateLimitsCapacityEnum::Global->value;

        $result = $this->rateLimiter->checkBucket(
            "rate:global",
            $capacity,
            RefillRatesEnum::Global->value
        );
        logger($result);
        if (! $result['ok']) {
            return response()->json([
                'error' => 'Global rate limit exceeded'
            ], 429)->withHeaders([
                'Retry-After'       => $result['retry_after'],
                'X-RateLimit-Global-Limit'  => $capacity,
                'X-RateLimit-Global-Remaining'  => 0
            ]);
        }

        $response = $next($request);

        $response->headers->set('X-RateLimit-Global-Limit', $capacity);
        $response->headers->set('X-RateLimit-Global-Remaining', $result['remaining']);
        return $response;
    }
}
