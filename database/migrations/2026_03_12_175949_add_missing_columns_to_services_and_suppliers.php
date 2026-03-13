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
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'ser_responsable')) {
                $table->string('ser_responsable')->nullable()->after('ser_code');
            }
            if (!Schema::hasColumn('services', 'ser_etage')) {
                $table->string('ser_etage')->nullable()->after('ser_responsable');
            }
        });

        Schema::table('fournisseurs', function (Blueprint $table) {
            if (!Schema::hasColumn('fournisseurs', 'fou_categorie')) {
                $table->string('fou_categorie')->nullable()->after('fou_nom');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['ser_responsable', 'ser_etage']);
        });

        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->dropColumn(['fou_categorie']);
        });
    }
};
