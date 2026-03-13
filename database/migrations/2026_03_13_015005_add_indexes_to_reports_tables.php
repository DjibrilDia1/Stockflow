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
        Schema::table('stocks_articles', function (Blueprint $table) {
            $table->index('sta_art_id');
            $table->index('sta_ent_id');
            $table->index('sta_quantite');
        });

        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->index('mvs_art_id');
            $table->index('mvs_ent_id');
            $table->index('mvs_date_mouvement');
            $table->index('mvs_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks_articles', function (Blueprint $table) {
            $table->dropIndex(['sta_art_id']);
            $table->dropIndex(['sta_ent_id']);
            $table->dropIndex(['sta_quantite']);
        });

        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->dropIndex(['mvs_art_id']);
            $table->dropIndex(['mvs_ent_id']);
            $table->dropIndex(['mvs_date_mouvement']);
            $table->dropIndex(['mvs_type']);
        });
    }
};
