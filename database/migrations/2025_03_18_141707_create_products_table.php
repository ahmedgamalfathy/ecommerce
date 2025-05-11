<?php

use App\Enums\Product\LimitedQuantity;
use App\Enums\Product\ProductStatus;
use App\Models\Product\Category;
use App\Traits\CreatedUpdatedByMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use CreatedUpdatedByMigration;
    /**
     * Run the migrations.
     */
    public function up(): void
    {//name ,description, price, status
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->boolean('is_limited_quantity')->default(LimitedQuantity::UNLIMITED->value);
            $table->smallInteger('quantity')->default(0);
            $table->tinyInteger('status')->default(ProductStatus::INACTIVE->value);
            $table->foreignIdFor(Category::class,'category_id')->nullable()->constrained();
            $table->foreignIdFor(Category::class,'sub_category_id')->nullable()->constrained();
            $this->CreatedUpdatedByRelationship($table);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
