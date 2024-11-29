<?php
// DEBUT - [SPECGT9] Ajout de la table état pour la gestion des états des chantiers mais potentiellement de tous les états de l'application
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $table = 'states';
    protected $primaryKey = 'code';
    public $incrementing = false;

    protected $fillable = ['code', 'status', 'label', 'status_group'];
}
// FIN - [SPECGT9] Ajout de la table état pour la gestion des états des chantiers mais potentiellement de tous les états de l'application
