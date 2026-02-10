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
        Schema::create('stocks_articles', function (Blueprint $table) {
            $table->id('sta_id');
            $table->foreignId('sta_art_id')
                ->constrained('articles', 'art_id')
                ->cascadeOnDelete();
            $table->foreignId('sta_ent_id')
                ->constrained('entrepots', 'ent_id')
                ->cascadeOnDelete();
            $table->integer('sta_quantite')->default(0);

            $table->unique(['sta_art_id', 'sta_ent_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks_articles');
    }
};
