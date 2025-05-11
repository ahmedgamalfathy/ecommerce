<?php

namespace App\Http\Controllers\Api\V1\Website\Order;

use App\Enums\IsMain;
use App\Models\Order\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\DiscountType;
use App\Models\Client\ClientEmail;
use App\Models\Client\ClientPhone;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Client\ClientAdrress;
use App\Enums\Product\LimitedQuantity;
use App\Services\Order\OrderItemService;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Http\Requests\Order\Website\CreateOrderRequest;

class OrderController extends Controller
{
    public $orderItemService;
    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function store(CreateOrderRequest $createOrderRequest)
    {
        try {
            DB::beginTransaction();
            $data =$createOrderRequest->validated();
             $client=Client::create([
                     'name'=>$data['name'],
                     'note'=>$data['note'],
              ]);
             if (isset($data['phones'])) {
                 foreach ($data['phones'] as $index => $phone) {
                     $primaryPhoneId = null;
                     $clientPhone = ClientPhone::create([
                         'client_id' => $client->id,
                         'phone' => $phone['phone'],
                         'country_code' => $data['countryCode'] ?? null,
                         'is_main' => ($index === 0) ? IsMain::PRIMARY->value : IsMain::SECONDARY->value,
                     ]);
                    if ($index === 0) {
                        $primaryPhoneId = $clientPhone->id;
                    }
                 }
             }
             if (isset($data['emails'])) {
                $primaryEmailId = null;
                 foreach ($data['emails'] as $index => $email) {
                    $clientEmail = ClientEmail::create([
                         'client_id' => $client->id,
                         'email' => $email['email'],
                        'is_main' => ($index === 0) ? IsMain::PRIMARY->value : IsMain::SECONDARY->value,
                     ]);
                    if ($index === 0) {
                        $primaryEmailId = $clientEmail->id;
                    }
                 }
             }
             if (isset($data['addresses'])) {
                $primaryAddressId = null;
                 foreach ($data['addresses'] as $index => $address) {
                  $clientAddress = ClientAdrress::create([
                         'client_id' => $client->id,
                         'address' => $address['address'],
                         'street_number'=>$address['streetNumber']??null,
                         'city'=>$address['city']??null,
                         'region'=>$address['region']??null,
                        'is_main' => ($index === 0) ? IsMain::PRIMARY->value : IsMain::SECONDARY->value,
                     ]);
                     if ($index === 0) {
                        $primaryAddressId = $clientAddress->id;
                    }
                 }
             }
             ////////////////order create////////////////////
             $totalCost =0;
             $totalPrice = 0;
             $totalPriceAfterDiscount = 0;

             $order = Order::create([
                 'discount' => $data['discount']??null,
                 'discount_type' => DiscountType::from($data['discountType'])->value,
                 'client_phone_id' => $primaryPhoneId,
                 'client_email_id' => $primaryEmailId,
                 'client_address_id' => $primaryAddressId,
                 'client_id' => $client->id,
                 'status' => OrderStatus::from($data['status'])->value,
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
            DB::commit();
            return ApiResponse::success([],__('crud.created'));
        } catch (\Throwable $th) {
            return ApiResponse::error(__('crud.server_error'),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

}

