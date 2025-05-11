<?php

namespace App\Http\Resources\Client\website;

use App\Enums\Client\ClientStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
                'clientUserId' => $this->id,
                'name' => $this->name,
                'avatar' => $this->avatar,
                'email' => $this->email,
        ];
    }
}
