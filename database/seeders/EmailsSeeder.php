<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

/**
 * Class EmailsSeeder
 * [SPECGT30] - Ajout des seeds pour les tables emails
 */
    class EmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template1 = new EmailTemplate;
        $template1->type = 'admin';
        $template1->label = 'Responsable';
        $template1->email = '***@tersys.fr';
        $template1->updated_by = 1;
        $template1->created_at = now();
        $template1->updated_at = now();
        $template1->save();

        $template2 = new EmailTemplate;
        $template2->type = 'accountant';
        $template2->label = 'Comptable';
        $template2->email = '***tersys.fr';
        $template2->updated_by = 1;
        $template2->created_at = now();
        $template2->updated_at = now();
        $template2->save();
    }
}

// FIN - [SPECGT9] - Ajout des états pour les chantiers
