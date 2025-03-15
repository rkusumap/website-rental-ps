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
        Schema::create('tb_transaction_detail', function (Blueprint $table) {
            $table->char('id_trd','36')->primary();

            $table->char('trx_trd','36')->nullable()->comment('id trx');
            $table->char('product_trd','36')->nullable()->comment('id product');

            $table->integer('order_trd')->nullable()->comment('order');

            $table->double('qty_trd')->nullable()->comment('quantity');
            $table->double('hpp_trd')->nullable()->comment('harga pokok pembelian');
            $table->double('price_trd')->nullable()->comment('harga jual');
            $table->double('total_trd')->nullable()->comment('total transaksi tanpa diskon, harga jual * quantity');
            $table->double('discount_persen_trd')->nullable()->comment('diskon persen');
            $table->double('discount_nominal_trd')->nullable()->comment('diskon nominal');
            $table->double('grand_total_trd')->nullable()->comment('total semua dengan diskon');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_transaction_detail');
    }
};
