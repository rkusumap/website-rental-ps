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
        Schema::create('ms_supplier', function (Blueprint $table) {
            $table->char('id_supplier','36')->primary();
            $table->text('name_supplier')->nullable(); // Text colum
            $table->text('phone_supplier')->unique()->nullable(); // Text colum
            $table->text('address_supplier')->nullable(); // Text colum
            $table->text('description_supplier')->nullable(); // Text colum
            $table->char('company_supplier','36');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_supplier');
    }
};
