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
        Schema::create('ms_product', function (Blueprint $table) {
            $table->char('id_product','36')->primary();
            $table->text('name_product')->nullable(); // Text colum
            $table->char('category_product','36')->nullable();
            $table->char('unit_product','36')->nullable();
            $table->char('brand_product','36')->nullable();
            $table->double('hpp_product')->nullable()->comment('harga pokok pembelian');
            $table->double('price_product')->nullable()->comment('harga jual');
            $table->char('company_product','36');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_product');
    }
};
