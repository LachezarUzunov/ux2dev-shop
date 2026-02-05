<?php

namespace App\Http\Middleware;

use App\Models\PartnerKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnerAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key');
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');

        if (!$apiKey || !$timestamp || !$signature) {
            return response()->json(['error' => 'Auth Headers Missing'], 401);
        }

        # Replay protection
        if (abs(time() - (int)$timestamp) > 300) {
            return response()->json(['error' => 'Timestamp Expired'], 401);
        }

        $prefix = substr($apiKey, 0, 8);
        $key = PartnerKey::whereKeyPrefix($prefix)->first();

        if (!$key || !hash_equals($key->key_hash, hash('sha256', $apiKey))) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }

        if ($key->revoked_at || $key->expires_at && now()->gt($key->expires_at)) {
            return response()->json(['error' => 'Expired API Key'], 401);
        }

        if (!$this->verifySignature($request, $timestamp, $signature, $key)) {
            return response()->json(['error' => 'Invalid Signature'], 401);
        }

        $request->attributes->set('partner', $key->partner);
        $request->attributes->set('partnerKey', $key);

        return $next($request);
    }

    protected function verifySignature($request, $timestamp, $signature, $key): bool
    {
        $secret = decrypt($key->secret_hash);
        $parsed = json_decode($request->getContent(), true);

        $canonical = json_encode($parsed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $bodyHash = hash('sha256', $canonical);

        $base =
            $request->method() . "\n" .
            $request->path() . "\n" .
            $timestamp . "\n" .
            $bodyHash;

        $calc = hash_hmac('sha256', $base, $secret);

        return hash_equals($calc, $signature);
    }
}
