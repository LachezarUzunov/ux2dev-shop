<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): OrderResource
    {
        $start = microtime(true);

        $order = Order::create([
            'partner_id'            => $request->attributes->get('partner')->id,
            'external_order_id'     => $request->externalOrderId,
            'amount'                => $request->amount,
            'details'               => $request->details,
        ]);

        $durationMs = (int)((microtime(true) - $start) * 1000);

        logger()->info('order_created', [
            'partner_id' => $order->partner_id,
            'order_id'   => $order->id,
            'duration_ms'=> $durationMs,
        ]);

        return new OrderResource($order);
    }
}
