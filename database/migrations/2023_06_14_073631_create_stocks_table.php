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
        Schema::disableForeignKeyConstraints();

        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('lot')->nullable();
            $table->integer('amount')->unsigned()->nullable();
            $table->longText('description')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('stock_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            // $table->foreign('stock_id')->references('id')->on('stocks');
            $table->unsignedBigInteger('product_id');
            // $table->foreign('product_id')->references('id')->on('products');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('stock_product');
    }
};
