<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * [SPECGT29] - Ajout des seeds pour les tables states, roles et users
     */
    public function run(): void
    {
        // exécuter le seed role
        $this->call(RoleSeeder::class);

        // exécuter le seed user
        $this->call(UserSeeder::class);

        // exécuter le seed state
        $this->call(StateSeeder::class);

        // exécuter le seed email
        $this->call(EmailsSeeder::class);

        // exécuter le seed tache_modele
        $this->call(TacheModeleSeeder::class);
    }
}
