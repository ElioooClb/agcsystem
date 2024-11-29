<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'id' => 1,
                'role' => 'administrateur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'role' => 'utilisateur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'role' => 'demo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
