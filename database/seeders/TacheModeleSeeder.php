<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TacheModeleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tache_modeles')->insert([
            ['libelle' => 'Préparation du chantier', 'ordre' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Installation des équipements', 'ordre' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Vérification sécurité', 'ordre' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Raccordement électrique', 'ordre' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Nettoyage final', 'ordre' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
