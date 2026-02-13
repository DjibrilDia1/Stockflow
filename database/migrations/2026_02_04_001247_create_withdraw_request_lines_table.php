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
        Schema::create('lignes_demande_sortie', function (Blueprint $table) {
            $table->id('lds_id');
            $table->foreignId('lds_dso_id')
                ->constrained('demandes_sortie', 'dso_id')
                ->cascadeOnDelete();
            $table->foreignId('lds_art_id')
                ->constrained('articles', 'art_id')
                ->restrictOnDelete();
            $table->foreignId('lds_ent_id')
                ->constrained('entrepots', 'ent_id')
                ->restrictOnDelete();
            $table->integer('lds_qte_demandee');
            $table->integer('lds_qte_servie')->default(0);
            $table->text('lds_note')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_demande_sortie');
    }
};
