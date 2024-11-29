<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'fonction',
        'acronyme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The chantiers that belong to the user.
     * @return BelongsToMany
     */
    public function chantiers(): BelongsToMany
    {
        return $this->belongsToMany(Chantier::class,  'chantier_user', 'user_id', 'chantier_id');
    }

    /**
     * The events that belong to the user.
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * The times that belong to the user.
     * @return HasMany
     */
    public function times(): HasMany
    {
        return $this->hasMany(Time::class);
    }

    /**
     * The chantiers supervised by the user.
     * [SPECGT3]
     * @return HasMany
     */
    public function supervisedChantiers(): HasMany
    {
        return $this->hasMany(Chantier::class, 'supervisor_id', 'id');
    }
}
