<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $reqId = $request->header('X-Request-Id') ?? (string) Str::uuid();
        $request->attributes->set('request_id', $reqId);

        logger()->withContext([
            'request_id'    => $reqId
        ]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $reqId);
        return $response;
    }
}
