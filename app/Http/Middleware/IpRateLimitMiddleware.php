<?php

namespace App\Http\Middleware;

use App\Enums\RateLimitsCapacityEnum;
use App\Enums\RefillRatesEnum;
use App\Services\RateLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpRateLimitMiddleware
{
    public function __construct(private RateLimitService $rateLimiter) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $capacity = RateLimitsCapacityEnum::GlobalIp->value;

        $result = $this->rateLimiter->checkBucket(
            "rate:ip:$ip",
            $capacity,
            RefillRatesEnum::GlobalIp->value
        );

        if (! $result['ok']) {
            return response()->json([
                'error' => 'IP rate limit exceeded'
            ], 429)->withHeaders([
                'Retry-After'       => $result['retry_after'],
                'X-RateLimit-IP-Limit'  => $capacity,
                'X-RateLimit-IP-Remaining'  => 0
            ]);
        }

        $response = $next($request);

        $response->headers->set('X-RateLimit-IP-Limit', $capacity);
        $response->headers->set('X-RateLimit-IP-Remaining', $result['remaining']);
        return $response;
    }
}
