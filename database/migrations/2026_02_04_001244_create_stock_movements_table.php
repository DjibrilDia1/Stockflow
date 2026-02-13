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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id('mvs_id');
            $table->foreignId('mvs_art_id')
                ->constrained('articles', 'art_id')
                ->restrictOnDelete();
            $table->foreignId('mvs_ent_id')
                ->constrained('entrepots', 'ent_id')
                ->restrictOnDelete();
            $table->enum('mvs_type', ['IN', 'OUT', 'ADJUST', 'TRANSFER']);
            $table->integer('mvs_quantite');
            $table->text('mvs_motif')->nullable();

            $table->foreignId('mvs_fou_id')
                ->nullable()
                ->constrained('fournisseurs', 'fou_id')
                ->nullOnDelete();
            $table->foreignId('mvs_ser_id')
                ->nullable()
                ->constrained('services', 'ser_id')
                ->nullOnDelete();
            $table->foreignId('mvs_usr_id')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->unsignedBigInteger('mvs_transfer_id')->nullable();
            $table->string('mvs_piece_jointe_url')->nullable();
            $table->dateTime('mvs_date_mouvement');
            $table->timestamp('mvs_created_at')->nullable();
            $table->timestamp('mvs_updated_at')->nullable();

            $table->index(['mvs_art_id', 'mvs_date_mouvement']);
            $table->index(['mvs_type', 'mvs_ent_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
