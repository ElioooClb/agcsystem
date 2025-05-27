<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomEvent extends Model
{
    protected $fillable = [
        'title',
        'start',
        'end',
        'allDay',
        'backgroundColor',
        'borderColor',
        'textColor',
        'url',
        'extendedProps'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'allDay' => 'boolean',
        'extendedProps' => 'array'
    ];
}
