<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RateLimitService
{
    public function __construct() {}

    public function checkBucket(string $key,int $capacity,float $refillRate, int $ttl = 3600)
    {
        try {
            $now = time();

            $data = Redis::get($key);
            if (! $data) {
                $tokens = $capacity;
                $last = $now;
            } else {
                [$tokens, $last] = explode(':', $data);
                $tokens = (float)$tokens;
                $last = (int)$last;
            }

            $elapsed = $now - $last;
            $tokens = min($capacity, $tokens + $elapsed * $refillRate);
            if ($tokens < 1) {
                $retryAfter = (int) ceil((1 - $tokens) / $refillRate);
                return [
                    'ok'            => false,
                    'remaining'     => 0,
                    'retry_after'   => $retryAfter
                ];
            }

            $tokens -= 1;

            Redis::setex($key, $ttl, "$tokens:$now");

            return [
                'ok' => true,
                'remaining' => (int) floor($tokens),
                'retry_after' => 0,
            ];

        } catch (\Throwable $e){
            logger()->warning('Redis Rate Limiter Failure', [
                'key'       => $key,
                'error'     => $e->getMessage(),
            ]);

            # Fail open
            return [
                'ok'            => true,
                'remaining'     => $capacity,
                'retry_after'   => 0,
                'degraded'      => true
            ];
        }
    }
}
