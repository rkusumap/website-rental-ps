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
        Schema::create('tb_stock_by_cashier', function (Blueprint $table) {
            $table->char('id_sbc','36')->primary();

            $table->char('product_sbc','36')->nullable()->comment('Product');
            $table->double('qty_sbc')->nullable()->comment('quantity');

            $table->integer('status_sbc')->nullable()->comment('apakah sudah di verif oleh admin');

            $table->char('company_sbc','36');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_stock_by_cashier');
    }
};
