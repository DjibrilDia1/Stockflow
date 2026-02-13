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
        Schema::create('articles', function (Blueprint $table) {
            $table->id('art_id');
            $table->string('art_reference')->unique();
            $table->string('art_nom');
            $table->string('art_unite');
            $table->foreignId('art_cat_id')
                ->constrained('categories', 'cat_id')
                ->restrictOnDelete();
            $table->integer('art_seuil_alerte')->default(0);
            $table->integer('art_stock_securite')->default(0);
            $table->decimal('art_prix_defaut', 10, 2)->nullable();
            $table->timestamp('art_created_at')->nullable();
            $table->timestamp('art_updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
