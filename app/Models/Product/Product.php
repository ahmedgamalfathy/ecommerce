<?php

namespace App\Models\Product;

use App\Enums\IsMain;
use App\Models\Slider\Slider;
use App\Traits\CreatedUpdatedBy;
use App\Enums\Product\ProductStatus;
use App\Models\Product\ProductMedia;
use App\Enums\Product\LimitedQuantity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use CreatedUpdatedBy, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cost',
        'price',
        'status',
        'is_limited_quantity',
        'quantity',
        'category_id',
        'sub_category_id'
    ];

    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'is_limited_quantity' => LimitedQuantity::class
        ];
    }
    public function productMedia()
    {
        return $this->hasMany(ProductMedia::class);
    }
    public function getFirstProductMedia()
    {
        return $this->hasMany(ProductMedia::class)->where('is_main',IsMain::PRIMARY)->limit(1)->get();
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
    public function sliders(){
        return $this->belongsToMany(Slider::class ,'slider_items');
    }

    public function getSimilarProduct() {
        return Product::where('category_id', $this->category_id)
                      ->orWhere('sub_category_id', $this->sub_category_id)
                      ->get();
    }
}
