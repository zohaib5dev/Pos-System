<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable()->unique();
            $table->text('description')->nullable();
            
            // Foreign keys with named constraints
            $table->foreignId('category_id')->nullable();
            $table->foreignId('brand_id')->nullable();
            $table->foreignId('unit_id')->nullable();
            
            $table->decimal('purchase_price', 15, 2)->default(0.00);
            $table->decimal('selling_price', 15, 2)->default(0.00);
            $table->decimal('wholesale_price', 15, 2)->default(0.00);
            $table->decimal('min_price', 15, 2)->default(0.00);
            $table->decimal('max_price', 15, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->boolean('allow_out_of_stock')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('main_image', 2048)->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('category_id', 'products_category_id_index');
            $table->index('brand_id', 'products_brand_id_index');
            $table->index('unit_id', 'products_unit_id_index');
            $table->index('is_active', 'products_is_active_index');
            $table->index('sku', 'products_sku_index');
            $table->index('barcode', 'products_barcode_index');
            $table->index('created_by', 'products_created_by_index');
        });

        // Add foreign keys after table creation with explicit names
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id', 'products_category_id_foreign')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
                  
            $table->foreign('brand_id', 'products_brand_id_foreign')
                  ->references('id')
                  ->on('brands')
                  ->onDelete('set null');
                  
            $table->foreign('unit_id', 'products_unit_id_foreign')
                  ->references('id')
                  ->on('units')
                  ->onDelete('set null');
                  
            $table->foreign('created_by', 'products_created_by_foreign')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign keys with explicit names
            $table->dropForeign('products_category_id_foreign');
            $table->dropForeign('products_brand_id_foreign');
            $table->dropForeign('products_unit_id_foreign');
            $table->dropForeign('products_created_by_foreign');
        });
        
        Schema::dropIfExists('products');
    }
};