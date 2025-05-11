<?php
namespace App\Services\Product;

use App\Helpers\ApiResponse;
use App\Models\Product\Product;
use Spatie\QueryBuilder\QueryBuilder;
use App\Enums\Product\LimitedQuantity;
use App\Services\Upload\UploadService;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\Product\FilterProduct;
use App\Services\ProductMedia\ProductMediaService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public  $productMediaService;
    public $uploadService;
    public function __construct(ProductMediaService $productMediaService ,UploadService $uploadService)
    {
        $this->productMediaService =$productMediaService;
        $this->uploadService =$uploadService;
    }
    public function allProducts(){
        return QueryBuilder::for(Product::class)
        ->allowedFilters([
            AllowedFilter::custom('search', new FilterProduct),
        ])
        ->get();
    }
    public function createProduct(array $data){
        $product= Product::create([
            'name'=>$data['name'],
            'price'=>$data['price'],
            'status'=>$data['status'],
            'description'=>$data['description']??null,
            'category_id'=>$data['categoryId']??null,
            'sub_category_id'=>$data['subCategoryId']??null,
            'quantity'=>$data['quantity']??0,
            'cost'=>$data['cost']??0,
            'is_limited_quantity'=>LimitedQuantity::from($data['isLimitedQuantity'])->value
        ]);
        foreach($data['productMedia'] as $media){
            $media['productId']=$product->id;
            $this->productMediaService->createProductMedia($media);
        }
        return $product;
    }
    public function editProduct(int $id){
        $product= Product::with(['category', 'productMedia'])->find($id);
        if(!$product){
            throw new ModelNotFoundException();
        }
        return $product;
    }
    public function updateProduct(int $id,array $data){
        $product= Product::find($id);
        $product->update([
            'name'=>$data['name'],
            'price'=>$data['price'],
            'status'=> $data['status'],
            'description'=>$data['description']??null,
            'category_id'=>$data['categoryId']??null,
            'sub_category_id'=>$data['subCategoryId']??null,
            'quantity'=>$data['quantity']??0,
            'cost'=>$data['cost']??0,
            'is_limited_quantity'=>LimitedQuantity::from($data['isLimitedQuantity'])->value
        ]);
        return $product;
    }
    public function deleteProduct(int $id){
        $product=Product::find($id);
        if(!$product){
           throw new ModelNotFoundException();
        }
        $product->delete();
    }

}
