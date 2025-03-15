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
        Schema::create('tb_rental', function (Blueprint $table) {
            $table->char('id_rental','36')->primary();
            $table->integer('user_rental')->nullable()->comment('User yang rental');
            $table->integer('one_day_rental')->nullable()->comment('rental satu hari, 1 = true, 0 = false');

            $table->double('grand_total_rental')->nullable();

            $table->date('date_start_rental')->nullable()->comment('tanggal awal');
            $table->date('date_akhir_rental')->nullable()->comment('tanggal awal');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_rental');
    }
};
