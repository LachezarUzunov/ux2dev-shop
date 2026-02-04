<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): OrderResource
    {
        logger('yes');
        $order = Order::create([
            'partner_id'            => 1,
            'external_order_id'     => $request->externalOrderId,
            'amount'                => $request->amount,
            'details'               => $request->details,
        ]);

        return new OrderResource($order);
    }
}
