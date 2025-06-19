<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChantierTache extends Model
{
    protected $table = 'chantier_taches';

    protected $fillable = ['chantier_id', 'libelle', 'ordre', 'done'];

    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }
}
