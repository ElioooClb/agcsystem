<?php
// DEBUT - [SPECGT9] - Table facturation des chantiers pour conserver le numéro de facture
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->date('requested_at')->nullable();
            $table->date('filled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('work_site_id')->nullable()->constrained('chantiers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
