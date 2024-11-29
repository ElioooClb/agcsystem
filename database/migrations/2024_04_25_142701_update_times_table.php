<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\TextUI\XmlConfiguration\SchemaFinder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DEBUT [SPECGT24] - Ajout de la colonne hours_travel et oncall_duty
        Schema::table('times', function (Blueprint $table) {
            $table->float('hours_travel')->nullable()->default(0.00)->after('hours_night');
            $table->tinyInteger('oncall_duty')->default(0)->after('hours_travel');
        });
        // FIN [SPECGT24] - Ajout de la colonne hours_travel et oncall_duty
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // DEBUT [SPECGT24] - Suppression de la colonne hours_travel et oncall_duty
        Schema::table('times', function (Blueprint $table) {
            $table->dropColumn('hours_travel');
            $table->dropColumn('oncall_duty');
        });
        // FIN [SPECGT24] - Suppression de la colonne hours_travel et oncall_duty
    }
};
