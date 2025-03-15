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
        Schema::create('ms_groupmodule', function (Blueprint $table) {
            $table->char('id_gmd','36')->primary();
            $table->text('level_gmd')->nullable(); // Text colum
            $table->text('module_gmd')->nullable(); // Text colum
            $table->text('action_gmd')->nullable(); // Text colum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_groupmodule');
    }
};
