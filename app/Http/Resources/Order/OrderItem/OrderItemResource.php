<?php

namespace App\Http\Resources\Order\OrderItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'orderItemId' => $this->id,
            'orderId' => $this->order_id,
            'price' => $this->price,
            'qty' => $this->qty,
            'cost'=>$this->cost,
            'product' => [
                'productId' => $this->product_id,
                'name' => $this->product->name,
            ]
        ];

    }
}
