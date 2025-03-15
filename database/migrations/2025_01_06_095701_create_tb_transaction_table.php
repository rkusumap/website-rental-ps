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
        Schema::create('tb_transaction', function (Blueprint $table) {
            $table->char('id_trx','36')->primary();
            $table->char('type_trx','1')->nullable()->comment('S: Sales, P: Purchase');
            $table->char('supplier_trx','36')->nullable()->comment('Supplier');
            $table->char('customer_trx','36')->nullable()->comment('Customer');
            $table->text('code_trx')->nullable()->comment('kode transaksi');
            $table->double('total_trx')->nullable()->comment('total transaksi tanpa diskon');
            $table->double('discount_persen_trx')->nullable()->comment('diskon persen');
            $table->double('discount_nominal_trx')->nullable()->comment('diskon nominal');
            $table->double('grand_total_trx')->nullable()->comment('total semua dengan diskon');
            $table->char('company_trx','36');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_transaction');
    }
};
