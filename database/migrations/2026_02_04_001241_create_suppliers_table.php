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
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id('fou_id');
            $table->string('fou_nom');
            $table->string('fou_contact_nom')->nullable();
            $table->string('fou_telephone')->nullable();
            $table->string('fou_email')->nullable();
            $table->text('fou_adresse')->nullable();
            $table->timestamp('fou_created_at')->nullable();
            $table->timestamp('fou_updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
