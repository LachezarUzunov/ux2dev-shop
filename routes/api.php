<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Middleware\IdempotencyMiddleware;
use App\Http\Middleware\PartnerAuthMiddleware;
use App\Http\Middleware\IpRateLimitMiddleware;
use App\Http\Middleware\GlobalRateLimitMiddleware;
use App\Http\Middleware\PartnerRateLimitMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware([GlobalRateLimitMiddleware::class,IpRateLimitMiddleware::class, PartnerAuthMiddleware::class, PartnerRateLimitMiddleware::class, IdempotencyMiddleware::class])->prefix('/v1')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});
