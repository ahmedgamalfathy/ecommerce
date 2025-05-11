<?php

namespace App\Http\Resources\Product\website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductMedia\ProductMediaResouce;

class AllProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'productId' => $this->id,
            'name' => $this->name,
            'path'=> ProductMediaResouce::collection($this->getFirstProductMedia()),
            'price' => $this->price,
            'status' => $this->status,
            'description' => $this->description,
        ];
    }
}
