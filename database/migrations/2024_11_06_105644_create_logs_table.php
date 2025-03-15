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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->text('user_id')->nullable(); // Text column
            $table->text('ip')->nullable(); // Text column
            $table->text('activity')->nullable(); // Text column
            $table->text('session_id')->nullable(); // Text column
            $table->text('name_table')->nullable(); // Text column
            $table->text('ref_id')->nullable(); // Text column
            $table->longText('json')->nullable(); // Text column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
