<?php

namespace App\Http\Resources\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllSliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sliderId' => $this->id,
            'name' => $this->name,
            'productsCount' => $this->products()->where('status', 1)->count(),
            // 'startDate' => $this->start_date,
            // 'endDate' => $this->end_date,
            'createdAt' => $this->created_at,
            'isActive' => $this->is_active,
        ];
    }
}
