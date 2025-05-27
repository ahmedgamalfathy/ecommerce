<?php

namespace App\Http\Controllers\Api\V1\Website\Order;

use App\Models\Order\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Enums\Order\DiscountType;
use App\Http\Controllers\Controller;
use App\Enums\Product\LimitedQuantity;
use App\Http\Resources\Order\Website\OrderResource;
use App\Services\Order\OrderItemService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AuthOrderController extends Controller implements HasMiddleware
{
    public $orderItemService;
    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }
    public static function middleware(): array
    {
        return [
            new Middleware('auth:client'),
        ];
    }

    public function store(Request $request){
        $auth = $request->user();
        $client = Client::findOrFail($auth->client_id);
        $data= $request->all();
        if(!$auth){
            return ApiResponse::error("Unauthenticated");
        }
        $totalCost =0;
        $totalPrice = 0;
        $totalPriceAfterDiscount = 0;

        $order = Order::create([
            'discount' =>0.00,
            'discount_type' =>0,
            'client_phone_id' => $request->input("client.clientPhoneId"),
            'client_email_id' => $request->input("client.clientEmailId"),
            'client_address_id' => $request->input("client.clientAddressId"),
            'client_id' => $client->id,
            'status' => 0,
        ]);

        $avilableQuantity = [];
        foreach ($data['orderItems'] as $itemData) {
            $item= $this->orderItemService->createOrderItem([
                    'orderId' => $order->id,
                    ...$itemData
                ]);

            if($item->product->is_limited_quantity == LimitedQuantity::LIMITED){
                if ($item->product->quantity < $item->qty) {
                    $avilableQuantity[] = [
                        'productId' => $item->product->id,
                        'quantity' => $item->product->quantity,
                        'name' => $item->product->name
                    ];
                    return  $avilableQuantity;
                }
               //  $item->product->decrement('quantity', $item->qty);
            }
            $totalPrice += $item->price * $item->qty;
            $totalCost += $item->cost*$item->qty;
        }

        if ($order->discount_type == DiscountType::PERCENTAGE) {
            $totalPriceAfterDiscount = $totalPrice - ($totalPrice * ($data['discount'] / 100));
        } elseif ($order->discount_type == DiscountType::FIXCED) {
            $totalPriceAfterDiscount = $totalPrice - $data['discount'];
        }elseif($order->discount_type == DiscountType::NO_DISCOUNT){
            $totalPriceAfterDiscount = $totalPrice;
        }
        $order->update([
            'price_after_discount' => $totalPriceAfterDiscount,
            'price' => $totalPrice,
            'total_cost'=>$totalCost
        ]);
        return ApiResponse::success(new OrderResource($order));
    }
}
