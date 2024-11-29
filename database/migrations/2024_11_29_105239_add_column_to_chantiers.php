<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chantiers', function (Blueprint $table) {
            $table->double('revised_hours')->default(0)->after('hours');

        });
        // Copier la valeur de 'hours' vers 'revised_hours' et remplacer null par 0
        DB::table('chantiers')->whereNull('hours')->update(['revised_hours' => 0]);
        DB::table('chantiers')->whereNotNull('hours')->update(['revised_hours' => DB::raw('hours')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chantiers', function (Blueprint $table) {
            $table->dropColumn('revised_hours');
        });
    }
};
