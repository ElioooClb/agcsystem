<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chantiers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
            $table->float('hours');
            $table->boolean('visible')
            ->default(true);
            $table->string('observation')
            ->nullable();

            $table->float('materialamount')
            ->nullable();
            $table->float('serviceamount')
            ->nullable();

            $table->string('color')
            ->nullable();
            $table->boolean('state')
            ->default(false);

            $table->boolean('visite')
            ->default(false);
            $table->boolean('bpe')
            ->default(false);
            $table->boolean('numero')
            ->default(false);
            $table->boolean('aiguillage')
            ->default(false);
            $table->boolean('tirage')
            ->default(false);
            $table->boolean('pto')
            ->default(false);
            $table->boolean('rop')
            ->default(false);
            $table->boolean('reflecto')
            ->default(false);
        });
    }

    public function down()
    {
        
        Schema::dropIfExists('chantiers');
    }
};
