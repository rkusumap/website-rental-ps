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
        Schema::create('tb_stock_opname', function (Blueprint $table) {
            $table->char('id_so','36')->primary();
            $table->char('type_so','1')->nullable()->comment('S: Sales, P: Purchase');
            $table->char('product_so','36')->nullable()->comment('Product');
            $table->double('qty_so')->nullable()->comment('quantity');
            $table->double('hpp_so')->nullable();
            $table->double('price_so')->nullable();
            $table->double('grand_total_so')->nullable()->comment('total semua price * quantity');
            $table->date('date_so')->nullable()->comment('tanggal');
            $table->integer('day_so')->nullable();
            $table->integer('month_so')->nullable();
            $table->integer('year_so')->nullable();
            $table->char('company_so','36');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_stock_opname');
    }
};
