<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => ('Admin'),
            'email' => ('nicolas@tersys.fr'),
            'password' => Hash::make('admin2024'),
            'role_id' => Role::where('role', 'administrateur')->first()->id,
            'acronyme' => 'ADM',
            'created_at' => now(),
            'updated_at' => now(),
            'fonction' => 'Président',
            'email_verified_at' => now(),
        ]);
    }
}
