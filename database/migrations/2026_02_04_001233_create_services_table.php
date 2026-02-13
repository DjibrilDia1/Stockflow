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
        Schema::create('services', function (Blueprint $table) {
            $table->id('ser_id');
            $table->string('ser_nom');
            $table->string('ser_code')->unique();
            $table->string('ser_type')->nullable();
            $table->timestamp('ser_created_at')->nullable();
            $table->timestamp('ser_updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
