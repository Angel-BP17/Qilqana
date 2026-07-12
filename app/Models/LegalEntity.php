<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class LegalEntity extends Model
{
    use HasFactory;

    protected $table = 'legal_entities';

    protected $fillable = [
        'ruc',
        'razon_social',
        'district',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function charges(): MorphMany
    {
        return $this->morphMany(Charge::class, 'interesado');
    }

    public function resolucions(): MorphToMany
    {
        return $this->morphToMany(Resolucion::class, 'interesado', 'resolucion_interesados');
    }

    /**
     * Obtiene todos los representantes que ha tenido la empresa.
     */
    public function representatives(): HasMany
    {
        return $this->hasMany(Representative::class, 'legal_entity_id');
    }

    /**
     * Obtiene el representante actual y activo.
     */
    public function representative(): HasOne
    {
        return $this->hasOne(Representative::class, 'legal_entity_id')
            ->where(function ($query) {
                $query->whereNull('fecha_hasta')
                    ->orWhere('fecha_hasta', '>=', now()->toDateString());
            })
            ->latestOfMany();
    }
}
