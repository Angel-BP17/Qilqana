<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaturalPerson extends Model
{
    use HasFactory;

    protected $table = 'natural_people';

    protected $fillable = [
        'dni',
        'cedula',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
    ];

    protected $appends = [
        'apellidos',
    ];

    public function getApellidosAttribute(): string
    {
        return trim(($this->apellido_paterno ?? '').' '.($this->apellido_materno ?? ''));
    }

    public function charges()
    {
        return $this->morphMany(Charge::class, 'interesado');
    }

    public function resolucions()
    {
        return $this->morphToMany(Resolucion::class, 'interesado', 'resolucion_interesados');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('dni', 'like', "%{$search}%")
                    ->orWhere('cedula', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        });
    }
}
