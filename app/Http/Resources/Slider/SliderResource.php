<?php

namespace App\Http\Resources\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Product\SliderProductResource;


class SliderResource extends JsonResource
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
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'isActive' => $this->is_active,
            'sliderItems'=> SliderProductResource::collection($this->products)
        ];
    }
}
