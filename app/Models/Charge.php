<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'n_charge',
        'charge_period',
        'document_date',
        'user_id',
        'resolucion_id',
        'tipo_interesado',
        'natural_person_id',
        'legal_entity_id',
        'asunto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolucion()
    {
        return $this->belongsTo(Resolucion::class);
    }

    public function naturalPerson()
    {
        return $this->belongsTo(NaturalPerson::class);
    }

    public function legalEntity()
    {
        return $this->belongsTo(LegalEntity::class);
    }

    public function signature()
    {
        return $this->hasOne(Signature::class);
    }
}
