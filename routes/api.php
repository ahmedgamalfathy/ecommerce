<?php

use App\Models\Order\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Select\SelectController;
use App\Http\Controllers\Api\V1\Dashboard\Auth\AuthController;
use App\Http\Controllers\Api\V1\Dashboard\User\UserController;
use App\Http\Controllers\Api\V1\Dashboard\Order\OrderController;
use App\Http\Controllers\Api\V1\Dashboard\Stats\StatsController;
use App\Http\Controllers\Api\V1\Dashboard\Client\ClientController;
use App\Http\Controllers\Api\V1\Dashboard\Slider\SliderController;
use App\Http\Controllers\Api\V1\Website\Order\AuthOrderController;
use App\Http\Controllers\Api\V1\Website\Payment\PaymentController;
use App\Http\Controllers\Api\V1\Website\Auth\AuthWebsiteController;
use App\Http\Controllers\Api\V1\Dashboard\Product\ProductController;
use App\Http\Controllers\Api\V1\Website\Order\ClientOrderController;
// use App\Http\Controllers\Api\V1\Dashboard\Category\CategoryController;
use App\Http\Controllers\Api\V1\Dashboard\User\UserProfileController;
use App\Http\Controllers\Api\V1\Website\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Website\Order\AuthOrderItemController;
use App\Http\Controllers\Api\V1\Website\Order\CheckQuantityController;
use App\Http\Controllers\Api\V1\Dashboard\Client\ClientEmailController;
use App\Http\Controllers\Api\V1\Dashboard\Client\ClientPhoneController;
use App\Http\Controllers\Api\V1\Website\Client\ClientWebsiteController;
// use App\Http\Controllers\Api\V1\Dashboard\Category\SubCategoryController;
use App\Http\Controllers\Api\V1\Dashboard\Client\ClientAdressController;
use App\Http\Controllers\Api\V1\Dashboard\User\ChangePasswordController;
use App\Http\Controllers\Api\V1\Dashboard\Category\CategoryTwoController;
use App\Http\Controllers\Api\V1\Website\Product\ProductWebsiteController;
use App\Http\Controllers\Api\V1\Website\Client\ClientEmailWebsiteController;
use App\Http\Controllers\Api\V1\Website\Client\ClientPhoneWebsiteController;
use App\Http\Controllers\Api\V1\Website\Auth\Profile\ClientProfileController;
use App\Http\Controllers\Api\V1\Website\Client\ClientAdressWebsiteController;
use App\Http\Controllers\Api\V1\Website\Product\BestSellingProductController;
use App\Http\Controllers\Api\V1\Dashboard\ProductMedia\ProductMediaController;
use App\Http\Controllers\Api\V1\Website\Order\OrderController as OrderWebsite;
use App\Http\Controllers\Api\V1\Website\Slider\SliderController as SliderWebsite;
use App\Http\Controllers\Api\V1\Website\Category\CategoryController as CategoryWebsite;
use App\Http\Controllers\Api\V1\Website\Auth\Profile\ChangePasswordController as ChangePasswordWebsite ;

Route::prefix('v1/admin')->group(function () {

    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('/login','login');
        Route::post('/logout','logout');
    });
    Route::apiResources([
        // "categories" => CategoryController::class,
        // "sub-categories" =>SubCategoryController::class,
        "category-two"=>CategoryTwoController::class,
        "product-media" => ProductMediaController::class,
        "products" => ProductController::class,
        "clients" => ClientController::class,
        "client-phones" => ClientPhoneController::class,
        "client-emails"=> ClientEmailController::class,
        "client-addresses"=>ClientAdressController::class,
        "orders" => OrderController::class,
        "sliders"=> SliderController::class
    ]);
    Route::apiResource('users', UserController::class);
    Route::apiSingleton('profile', UserProfileController::class);
    Route::put('profile/change-password', ChangePasswordController::class);
    Route::get('/stats',StatsController::class);

});//admin
Route::prefix('v1/website')->group(function(){
    Route::controller(AuthWebsiteController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });
    Route::controller(ForgotPasswordController::class)->prefix("/forgotPassword")->group(function(){
        Route::post("sendCode","sendCodeEmail");
        Route::post('verifyCode','verifyCodeEmail');
        Route::post('resendCode','resendCode');
        Route::post('newPassword','newPassword');
    });
    Route::apiSingleton('profile', ClientProfileController::class)->names([
        'show' => 'profileWeb.show',
        'update'=>'profileWeb.update'
    ]);
    Route::post('logout',[AuthWebsiteController::class ,'logout'])->middleware('auth:client');
    Route::apiResource('client-orders',ClientOrderController::class)->only(['index','show']);
    Route::apiResource('sliders', SliderWebsite::class)->only(['index'])->names([
        'index' => 'slidersWeb.index',
    ]);
    Route::apiResource('categories',CategoryWebsite::class)->only(['index']);
    Route::apiResource('products',ProductWebsiteController::class)->only(['index','show'])->names([
        'index' => 'productsWeb.index',
        'show'=>'productsWeb.show'
    ]);
    Route::apiResource('orders',OrderWebsite::class)->only(['store'])->names([
        'store' => 'ordersWeb.store',
    ]);
    Route::apiResource("clients-web" , ClientWebsiteController::class)->only(['index']);
    Route::apiResource("client-web-phones" , ClientPhoneWebsiteController::class);
    Route::apiResource("client-web-addresses",ClientAdressWebsiteController::class);
    Route::apiResource("client-web-emails",ClientEmailWebsiteController::class);
    Route::get('check-Quantity',CheckQuantityController::class);
    Route::put('change-password', ChangePasswordWebsite::class);
    Route::get('/BestSellingProducts',[BestSellingProductController::class ,'BestSellingProducts']);
    Route::get('/BestSellingProductsDetail/{id}',[BestSellingProductController::class ,'BestSellingProductsDetail']);
    Route::post('orders-auth',[AuthOrderController::class,'store']);
    Route::get('orders-auth/{id}',[AuthOrderController::class,'show']);
    Route::put('orders-update/{id}',[AuthOrderController::class,'update']);
    Route::get('/orderItems',[AuthOrderItemController::class , 'index']);
    Route::get('/orderItems/{id}',[AuthOrderItemController::class , 'edit']);
    Route::post('/orderCreate',[AuthOrderItemController::class , 'store']);
    Route::put('/orderUpdate/{id}',[AuthOrderItemController::class , 'update']);
    Route::delete('/orderDelete/{id}',[AuthOrderItemController::class , 'destory']);
    Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
});//website ...
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
Route::prefix('v1/selects')->group(function(){
  Route::get('', [SelectController::class, 'getSelects']);
});
