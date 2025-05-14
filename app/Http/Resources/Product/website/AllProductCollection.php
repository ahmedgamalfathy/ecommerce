<?php

namespace App\Http\Resources\Product\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Product\Website\AllProductResource;

class AllProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    private $pagination;
    public function __construct($resource)
    {
        if ($resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $this->pagination = [
                'total' => $resource->total(),
                'count' => $resource->count(),
                'perPage' => $resource->perPage(),
                'currentPage' => $resource->currentPage(),
                'totalPages' => $resource->lastPage()
            ];
            $resource = $resource->getCollection();
        } else {
            $this->pagination = [
                'total' => 0,
                'count' => 0,
                'perPage' => 0,
                'currentPage' => 1,
                'totalPages' => 1
            ];
        }

        parent::__construct($resource);
    }
    public function toArray(Request $request): array
    {
        return [
            'products' => AllProductResource::collection($this->collection),
            'pagination' => $this->pagination
           ];
    }
}
