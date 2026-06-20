<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsuntoType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relación con los tipos de resolución permitidos.
     */
    public function resolucionTypes(): BelongsToMany
    {
        return $this->belongsToMany(ResolucionType::class, 'asunto_type_resolucion_type');
    }

    /**
     * Relación con las resoluciones.
     */
    public function resolucions(): HasMany
    {
        return $this->hasMany(Resolucion::class);
    }
}
