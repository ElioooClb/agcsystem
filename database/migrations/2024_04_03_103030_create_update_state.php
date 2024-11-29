<?php
// DEBUT - [SPECGT9] - Ajout de la table état pour la gestion des états des chantiers mais potentiellement de tous les états de l'application

use Database\Seeders\StateSeeder;
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
        // Creating the states table
        Schema::create('states', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('status');
            $table->string('label');
            $table->string('status_group');
        });

        // Inserting the default state
        DB::table('states')->insert([
            ['code' => 'DEFAULT', 'status' => 'idle', 'label' => 'default', 'status_group' => 'idle'],
            ['code' => 'ST_FACT1A', 'status' => 'initial', 'label' => 'Demander la facturation', 'status_group' => 'inProgress'],
        ]);

        // Removing the state column to the chantiers table
        Schema::table('chantiers', function (Blueprint $table) {
            // Supprimer la colonne state
            $table->dropColumn('state');
        });
        // Adding the state column to the chantiers table
        Schema::table('chantiers', function (Blueprint $table) {
            // Recréer la colonne state
            $table->string('state')->default('ST_FACT1A')->nullable();
            $table->foreign('state')->references('code')->on('states');
        });

        // // Updating the chantiers table
        // DB::table('chantiers')
        //     ->where('state', '=', 'DEFAULT')
        //     ->update(['state' => "ST_FACT1A"]);

        // DB::table('chantiers')
        //     ->where('visible', '=', '0')
        //     ->update(['state' => "ST_FACT1C"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chantiers', function (Blueprint $table) {
            // Supprimer la colonne state
            $table->dropForeign(['state']);
            $table->dropColumn('state');
        });
        Schema::table('chantiers', function (Blueprint $table) {
            // Recréer la colonne state
            $table->string('state');
        });
        Schema::dropIfExists('states');
    }
};
// FIN - [SPECGT9] - Ajout de la table état pour la gestion des états des chantiers mais potentiellement de tous les états de l'application
