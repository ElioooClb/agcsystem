<?php

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
        Schema::table('chantiers', function (Blueprint $table) {
            $table->dropColumn(['visite', 'bpe', 'numero', 'aiguillage', 'tirage', 'pto', 'rop', 'reflecto']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chantiers', function (Blueprint $table) {
            $table->string('visite')->nullable();
            $table->string('bpe')->nullable();
            $table->string('numero')->nullable();
            $table->string('aiguillage')->nullable();
            $table->string('tirage')->nullable();
            $table->string('pto')->nullable();
            $table->string('rop')->nullable();
            $table->string('reflecto')->nullable();
        });
    }
};
