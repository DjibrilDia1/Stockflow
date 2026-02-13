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
        Schema::create('demandes_sortie', function (Blueprint $table) {
            $table->id('dso_id');
            $table->foreignId('dso_ser_id')
                ->constrained('services', 'ser_id')
                ->restrictOnDelete();
            $table->foreignId('dso_demandeur_id')
                ->constrained('users', 'id')
                ->restrictOnDelete();
            $table->enum('dso_statut', ['DRAFT', 'APPROVED', 'FULFILLED', 'REJECTED'])->default('DRAFT');
            $table->timestamp('dso_created_at')->nullable();
            $table->timestamp('dso_updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_sortie');
    }
};
