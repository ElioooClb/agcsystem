<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ← À ajouter
use App\Models\Role;

public function run(): void
{
    // Insère les rôles s'ils n'existent pas déjà
    $roles = [
        1 => 'administrateur',
        2 => 'utilisateur',
        3 => 'demo',
        4 => 'superintendant',
    ];

    foreach ($roles as $id => $role) {
        \App\Models\Role::updateOrCreate(
            ['id' => $id],
            ['role' => $role, 'updated_at' => now()]
        );
    }
}