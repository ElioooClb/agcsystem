<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EmailTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $template3 = new EmailTemplate;
        $template3->type = 'worker_01';
        $template3->label = 'Conducteur de travaux';
        $template3->email = '***@tersys.fr';
        $template3->updated_by = 1;
        $template3->created_at = now();
        $template3->updated_at = now();
        $template3->save();

        $template4 = new EmailTemplate;
        $template4->type = 'worker_02';
        $template4->label = 'Bureau d\'études';
        $template4->email = '***@tersys.fr';
        $template4->updated_by = 1;
        $template4->created_at = now();
        $template4->updated_at = now();
        $template4->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\EmailTemplate::whereIn('type', ['worker_01', 'worker_02'])->delete();
    }
};
