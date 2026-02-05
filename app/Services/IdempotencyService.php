<?php

namespace App\Services;

use App\Models\IdempotencyKey;
use Closure;
use Illuminate\Support\Facades\Cache;

class IdempotencyService
{
    public function __construct() {}

    public function handle($request, $key, Closure $next)
    {
        #$partnerId = $request->partnerId;
        $partnerId = 1;
        $hash = hash('sha256', json_encode($request->all()));
        return $this->lockKey($request, $key, $hash, $partnerId, $next);
    }

    protected function lockKey($request, $key, $hash, $partnerId, $next)
    {
        $lock = Cache::lock("idempotency:$partnerId:$hash", 10);
        return $lock->block(5, function () use ($request, $key, $hash, $partnerId, $next) {
            $record = IdempotencyKey::wherePartnerId($partnerId)->whereIdempotencyKey($key)->first();

            if ($record)
                return $this->handleFoundKey($record, $hash);

            $response = $next($request);
            $this->createIdempotencyKey($key, $hash, $response);
            return $response;
        });
    }

    protected function createIdempotencyKey($key, $hash, $response): void
    {
        IdempotencyKey::create([
            'partner_id'        => 1,
            'idempotency_key'   => $key,
            'request_hash'      => $hash,
            'response_body'     => $response->getContent(),
            'response_code'     => $response->getStatusCode(),
            'expires_at'        => now()->addHours(24)
        ]);
    }

    protected function handleFoundKey($record, $hash)
    {
        if ($record->request_hash !== $hash) {
            abort(409, 'Same idempotency key used with different payload.');
        }

        return response()->json(
            json_decode($record->response_body, true),
            $record->response_code
        );
    }
}
