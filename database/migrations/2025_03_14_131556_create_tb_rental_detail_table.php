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
        Schema::create('tb_rental_detail', function (Blueprint $table) {
            $table->char('id_rtd','36')->primary();

            $table->char('rental_rtd','36')->nullable()->comment('referensi id rental');
            $table->char('product_rtd','36')->nullable()->comment('Product');
            $table->date('date_rtd')->nullable()->comment('tanggal');
            $table->double('price_rtd')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_rental_detail');
    }
};
