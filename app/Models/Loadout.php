<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Loadout extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function parameters()
    {
        return $this->belongsToMany(Parameter::class, 'loadout_parameter');
    }

    public function chantiers()
    {
        return $this->hasMany(Chantier::class, 'loadout_id');
    }
}
