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
        // Insertion des données dans la table 'states'
        DB::table('states')->insert([
            [
                'code' => 'STAGE_1A',
                'status' => 'Not Started',
                'label' => 'Bon de travaux non commencé',
                'status_group' => 'staging'
            ],
            [
                'code' => 'STAGE_1B',
                'status' => 'Started',
                'label' => 'Bon de travaux en cours',
                'status_group' => 'staging'
            ],
            [
                'code' => 'STAGE_1C',
                'status' => 'Finished',
                'label' => 'Bon de travaux terminé',
                'status_group' => 'staging'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('states')->where('code', 'STAGE_1A')->delete();
        DB::table('states')->where('code', 'STAGE_1B')->delete();
        DB::table('states')->where('code', 'STAGE_1C')->delete();
    }
};
