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
        Schema::create('entrepots', function (Blueprint $table) {
            $table->id('ent_id');
            $table->string('ent_nom');
            $table->string('ent_code')->unique();
            $table->string('ent_localisation')->nullable();
            $table->timestamp('ent_created_at')->nullable();
            $table->timestamp('ent_updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrepots');
    }
};
