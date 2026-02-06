<?php

namespace App\Http\Middleware;

use App\Enums\RateLimitsCapacityEnum;
use App\Enums\RefillRatesEnum;
use App\Services\RateLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnerRateLimitMiddleware
{
    public function __construct(private RateLimitService $rateLimiter) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $partner = $request->attributes->get('partner');
        $partnerKey = $request->attributes->get('partnerKey');

        $capacity = RateLimitsCapacityEnum::fromName($partnerKey->rate_limit_plan)->value;
        $refillRate = RefillRatesEnum::fromName($partnerKey->rate_limit_plan)->value;

        $result = $this->rateLimiter->checkBucket(
            "rate:partner:{$partner->id}",
            $capacity,
            $refillRate
        );

        if (!$result['ok']) {
            return response()->json([
                'error' => 'Partner rate limit exceeded'
            ], 429)->withHeaders([
                'Retry-After' => $result['retry_after'],
                'X-RateLimit-Partner-Limit' => $capacity,
                'X-RateLimit-Partner-Remaining' => 0,
            ]);
        }

        $response = $next($request);

        $response->headers->set('X-RateLimit-Partner-Limit', $capacity);
        $response->headers->set(
            'X-RateLimit-Partner-Remaining',
            $result['remaining']
        );

        return $response;
    }
}
