<?php

namespace App\Http\Controllers\Api\V1\Website\Order;

use App\Models\Order\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Symfony\Component\HttpKernel\Profiler\Profile;

use App\Http\Resources\Client\website\ProfileResource;
use App\Http\Resources\Order\OrderItem\Website\OrderItemResource;

class ClientOrderController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth:client'),
        ];
    }

    public function index(Request $request)
    {
        $clientId=$request->user()->client_id;
        $clientOrder = Client::with('orders')->where('id',$clientId)->first();
        return ApiResponse::success(new ProfileResource($clientOrder));
    }
    public function show(int $id)
    {
        $order = Order::with('items')->where('id',$id)->first();
        return ApiResponse::success(OrderItemResource::collection($order->items));
    }
}
