<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Product Name');
            $table->string('slug')->comment('Product Slug');
            $table->string('sku')->comment('Product SKU');
            $table->text('description')->comment('Product Description');
            $table->decimal('price',10,2)->comment('Product Price');
            $table->enum('status', ['active','inactive'])->default('active')->comment('Product Status');
            $table->softDeletes();
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
