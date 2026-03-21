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
        // TABLE : mouvements_stock
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->index('mvs_date_mouvement', 'idx_mvs_date');
            $table->index('mvs_art_id', 'idx_mvs_article');
            $table->index('mvs_ent_id', 'idx_mvs_entrepot');
            $table->index('mvs_type', 'idx_mvs_type');
            $table->index(['mvs_type', 'mvs_ent_id'], 'idx_mvs_type_ent');
        });

        // TABLE : articles
        Schema::table('articles', function (Blueprint $table) {
            $table->index('art_reference', 'idx_articles_ref');
            $table->index('art_nom', 'idx_articles_nom');
            $table->index('art_cat_id', 'idx_articles_cat');
        });

        // TABLE : stocks_articles
        Schema::table('stocks_articles', function (Blueprint $table) {
            // Utilisation correcte des noms de colonnes de la base
            $table->unique(['sta_art_id', 'sta_ent_id'], 'idx_stocks_item_ent');
        });

        // TABLE : sessions
        Schema::table('sessions', function (Blueprint $table) {
            $table->index('user_id', 'idx_sessions_user');
            $table->index('last_activity', 'idx_sessions_last');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // TABLE : mouvements_stock
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->dropIndex('idx_mvs_date');
            $table->dropIndex('idx_mvs_article');
            $table->dropIndex('idx_mvs_entrepot');
            $table->dropIndex('idx_mvs_type');
            $table->dropIndex('idx_mvs_type_ent');
        });

        // TABLE : articles
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('idx_articles_ref');
            $table->dropIndex('idx_articles_nom');
            $table->dropIndex('idx_articles_cat');
        });

        // TABLE : stocks_articles
        Schema::table('stocks_articles', function (Blueprint $table) {
            $table->dropUnique('idx_stocks_item_ent');
        });

        // TABLE : sessions
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('idx_sessions_user');
            $table->dropIndex('idx_sessions_last');
        });
    }
};
