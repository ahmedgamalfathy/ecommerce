<?php

namespace App\Http\Resources\Order\OrderItem\Website;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductMedia\Website\ProductMediaResouce;



class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order' => [
                "orderId"=>$this->order->id,
                'number' => $this->order->number ?? "",
                'phone'=>  $this->order->clientPhone->phone??"",
                'address'=>  $this->order->clientAddress->address??"",
                'email'=>  $this->order->clientEmail->email??"",
                'status' => $this->order->status ?? "",
                "price" =>  $this->order->price ??"",
                "discount"  => $this->order->discount ??"",
                'priceAfterDiscount' => $this->order->price_after_discount ?? "",
                'date' => $this->order->created_at?->toDateTimeString(),
            ],
            'orderId' => $this->order_id,
            'orderItemId' => $this->id,
            'price' => $this->price,
            'qty' => $this->qty,
            // 'cost'=>$this->cost,
            'product' => [
                'productId' => $this->product_id,
                'name' => $this->product->name,
                'path'=> ProductMediaResouce::collection($this->product->productMedia) ,
            ]
        ];

    }
}
