<?php

namespace App\Http\Controllers\Api\V1\Website\Product;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Utils\PaginateCollection;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\Product\FilterProduct;
use Spatie\QueryBuilder\AllowedFilter;
use App\Services\Product\ProductService;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Http\Resources\Product\Website\AllProductCollection;
use App\Http\Resources\Product\Website\ProductResource;

class ProductController extends Controller
{
    public $productService;
    public function __construct( ProductService $productService)
    {
        $this->productService =$productService;
    }
    public function index(Request $request)
    {
         $products= QueryBuilder::for(Product::class)->allowedFilters(['status',
                    AllowedFilter::exact('categoryId', 'category_id'),
                    AllowedFilter::exact('subCategoryId', 'sub_category_id'),
                    AllowedFilter::custom('search', new FilterProduct),
                    AllowedFilter::callback('price', function ($query, $value) {
                    if (is_string($value)) {
                        $value = explode(',', $value); // تحويل النص إلى مصفوفة
                        return $query->whereBetween('price', [$value[0], $value[1]]);
                    }
                    if (is_array($value) && count($value) === 2) {
                        return $query->whereBetween('price', [$value[0], $value[1]]);
                    }
                    return $query;
                    }),
                    ])->get();
        return ApiResponse::success(new AllProductCollection(PaginateCollection::paginate($products, $request->pageSize?$request->pageSize:10)));
    }
    public function show(int $id)
    {
      $product=Product::with(['productMedia'])->find($id);
      if(!$product){
        return  ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
      }
     $product->getSimilarProduct();
     $product->getFirstProductMedia();
      return ApiResponse::success(new ProductResource($product));
    }
}
