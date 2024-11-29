<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the supervisor column.
        Schema::table('chantiers', function (Blueprint $table) {
            $table->unsignedBigInteger('supervisor_id')->nullable();
        });

        // Define a default supervisor.
        $defaultSupervisor = 1; // Replace this with the ID of the default supervisor.

        // Set the default supervisor for existing chantiers.
        DB::table('chantiers')->update(['supervisor_id' => $defaultSupervisor]);

        // Now modify the supervisor column to add foreign key constraint.
        Schema::table('chantiers', function (Blueprint $table) {
            $table->foreign('supervisor_id')->references('id')->on('users')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chantiers', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};
