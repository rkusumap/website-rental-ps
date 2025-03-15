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
        Schema::create('ms_level', function (Blueprint $table) {
            $table->char('id_level','36')->primary();
            $table->text('code_level')->nullable(); // Text column
            $table->text('name_level')->nullable(); // Text column
            $table->text('user_level')->nullable(); // Text column
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_level');
    }
};
