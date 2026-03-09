<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaturalPerson extends Model
{
    use HasFactory;

    protected $table = 'natural_people';

    protected $fillable = [
        'dni',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
    ];

    protected $appends = [
        'apellidos',
    ];

    public function getApellidosAttribute(): string
    {
        return trim(($this->apellido_paterno ?? '') . ' ' . ($this->apellido_materno ?? ''));
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }
}
