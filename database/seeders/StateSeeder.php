<?php
// DEBUT - [SPECGT9] - Ajout des états pour les chantiers

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('states')->insert([
            // Work sites states
            // ['code' => 'ST_FACT1A', 'status' => 'initial', 'label' => 'Demander la facturation', 'status_group' => 'inProgress'],
            ['code' => 'ST_FACT2B', 'status' => 'partiallyBilled', 'label' => 'Info : Facturation partielle', 'status_group' => 'toBill'],
            ['code' => 'ST_FACT1B', 'status' => 'pendingApproval', 'label' => 'Demander la facturation', 'status_group' => 'inProgress'],
            ['code' => 'ST_FACT2A', 'status' => 'billable', 'label' => 'Info : Facturation finale', 'status_group' => 'toBill'],
            ['code' => 'ST_FACT3A', 'status' => 'pendingArchiving', 'label' => 'Archiver le chantier', 'status_group' => 'toBill'],
            ['code' => 'ST_FACT1C', 'status' => 'archived', 'label' => 'Archivé', 'status_group' => 'archived'],
        ]);
    }
}

// FIN - [SPECGT9] - Ajout des états pour les chantiers
