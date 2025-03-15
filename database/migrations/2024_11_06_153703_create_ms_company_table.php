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
        Schema::create('ms_company', function (Blueprint $table) {
            $table->char('id_company','36')->primary();
            $table->text('name_company')->nullable(); // Text colum
            $table->text('phone_company')->nullable(); // Text colum
            $table->text('address_company')->nullable(); // Text colum
            $table->text('description_company')->nullable(); // Text colum
            $table->text('user_company')->nullable(); // Text column
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_company');
    }
};
