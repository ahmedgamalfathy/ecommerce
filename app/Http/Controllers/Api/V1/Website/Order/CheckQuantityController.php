<?php

namespace App\Http\Controllers\Api\V1\Website\Order;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;

class CheckQuantityController extends Controller
{
    public function __invoke(Request $request){
      $data=$request->all();
      $product= Product::find($data['productId']);
      if(!$product){
        return  ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
      }
      if($product->quantity >= $data['qty']){
        return  ApiResponse::success([
            "availability"=>true,
            "qty"=>$product->quantity
        ]);
      }else{
        return  ApiResponse::success([
            "availability"=>false,
            "qty"=>$product->quantity
        ]);
      }
    }
}
