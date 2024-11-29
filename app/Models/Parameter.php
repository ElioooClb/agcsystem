<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $fillable = ['label'];

    public function loadouts()
    {
        return $this->belongsToMany(Loadout::class);
    }

    public function chantiers()
    {
        return $this->belongsToMany(Chantier::class)->withPivot('completed');
    }
}
