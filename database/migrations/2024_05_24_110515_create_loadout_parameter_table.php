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
        Schema::create('loadout_parameter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loadout_id');
            $table->unsignedBigInteger('parameter_id');
            $table->timestamps();

            $table->foreign('loadout_id')->references('id')->on('loadouts')->onDelete('cascade');
            $table->foreign('parameter_id')->references('id')->on('parameters')->onDelete('cascade');

            $table->unique(['loadout_id', 'parameter_id'], 'loadout_parameter_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loadout_parameter');
    }
};
