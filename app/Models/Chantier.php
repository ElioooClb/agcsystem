<?php

namespace App\Models;

use App\Models\User;
use App\Models\Time;
use App\Models\Event;
use App\Models\State;
use App\Models\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Chantier extends Model
{
    use HasFactory;
    // DEBUT [SPECGT9] - Modification des propriétés de la table chantier
    protected $table = 'chantiers';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['title', 'hours', 'serviceamount', 'revised_hours', 'realisation_date'];

    /**
     * This function listens for the CRUD operations on the invoice table
     * Listens if the state of the work site leaves the initial state and creates or updates an invoice
     * Version: 2.2
     * [SPECGT21]
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($workSite) {
            $workSite->revised_hours = $workSite->hours ?? 0;
        });
        static::updating(function ($workSite) {
            // Check if the state of the work site leaves the initial state
            if (($workSite->states->code === 'ST_FACT1A' || $workSite->states->code === 'ST_FACT1B') && $workSite->isDirty('state')) {
                Invoice::updateOrCreateInvoice($workSite, ['requested_at' => Carbon::now()]);
            }
        });
    }

    /**
     * This function call for the user table dependencies of the chantier
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chantier_user', 'chantier_id', 'user_id');
    }

    /**
     * This function call for the event table dependencies of the chantier
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'id_chantier');
    }

    /**
     * This function call for the time table dependencies of the chantier
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function times(): HasMany
    {
        return $this->hasMany(Time::class, 'chantier_id');
    }

    /**
     * This function call for the state table dependencies of the chantier
     * @return BelongsTo
     */
    public function states(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state', 'code');
    }

    /**
     * This function call for the invoice table dependencies of the chantier
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoices(): HasOne
    {
        return $this->HasOne(Invoice::class, 'work_site_id');
    }
    // FIN [SPECGT9] - Ajout d'un hook pour charger les dépenances de la table time

    /**
     * This function call for the user table dependencies of the chantier
     * [SPECGT3]
     * @return BelongsTo
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Getter for the idAff attribute
     * [SPECGT3]
     * @return string
     */
    public function getIdAff(): string
    {
        return 'idAff_' . $this->id;
    }

    // Début [SPECGT6] - Ajout des loadouts et des paramètres aux chantiers
    public function parameters(): BelongsToMany
    {
        return $this->belongsToMany(Parameter::class)->withPivot('completed');
    }

    public function loadout()
    {
        return $this->belongsTo(Loadout::class, 'loadout_id');
    }
    // Fin [SPECGT6] - Ajout des loadouts et des paramètres aux chantiers
}
