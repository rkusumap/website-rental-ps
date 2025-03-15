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
        Schema::create('tb_log_stock_by_cashier', function (Blueprint $table) {
            $table->char('id_lsbc','36')->primary();

            $table->char('product_lsbc','36')->nullable()->comment('Product');
            $table->double('qty_lsbc')->nullable()->comment('quantity');

            $table->char('company_lsbc','36');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_log_stock_by_cashier');
    }
};
