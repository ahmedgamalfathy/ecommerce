<?php

namespace App\Http\Resources\Product;

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
            'path'=>$this->productMedia->isNotEmpty() ?ProductMediaResouce::collection($this->productMedia->take(1)): url("storage/".'ProductMedia/default-product.jpg'),
            'name' => $this->name,
            'price' => $this->price,
            'status' => $this->status,
            "categoryId" => $this->category_id??"",
            "subCategoryId"=> $this->sub_category_id??"",
            'description' => $this->description,
        ];
    }
}
