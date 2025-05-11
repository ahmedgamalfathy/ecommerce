<?php

namespace App\Http\Resources\Order\Website;

use App\Http\Resources\Order\OrderItem\OrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'orderId' => $this->id,
            'orderNumber' => $this->number,
            'status' => $this->status,
            'price' => $this->price,
            'priceAfterDiscount' => $this->price_after_discount,
            'products' =>count($this->items),
            // 'orderItems'=> OrderItemResource::collection($this->items),
        ];
    }
}
