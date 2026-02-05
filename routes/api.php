<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Middleware\IdempotencyMiddleware;


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

# Include auth routes
//include __DIR__ . '/auth.php';

########################
## Authorized routes ###
########################
//Route::middleware(['auth:sanctum', 'verified'])->prefix('/v1')->group(function () {
Route::prefix('/v1')->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->middleware([IdempotencyMiddleware::class]);
});
