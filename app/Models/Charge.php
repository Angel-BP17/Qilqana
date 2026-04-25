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

    /**
     * Obtener el representante a través de la entidad legal.
     */
    public function getRepresentativeAttribute()
    {
        return $this->legalEntity?->representative;
    }

    public function signature()
    {
        return $this->hasOne(Signature::class);
    }

    /* Accessors para optimizar vistas */

    public function getInteresadoLabelAttribute(): string
    {
        switch ($this->tipo_interesado) {
            case 'Persona Juridica':
                return $this->legalEntity?->razon_social ?: $this->legalEntity?->ruc ?: '---';
            case 'Persona Natural':
                $person = $this->naturalPerson;
                if (! $person) {
                    return '---';
                }
                $fullName = trim(($person->nombres ?? '').' '.($person->apellido_paterno ?? '').' '.($person->apellido_materno ?? ''));

                return $fullName !== '' ? $fullName : ($person->dni ?? '---');
            case 'Trabajador UGEL':
                return trim(($this->user?->name ?? '').' '.($this->user?->last_name ?? '')) ?: '---';
            default:
                return '---';
        }
    }

    public function getHasSignatureAttribute(): bool
    {
        return (bool) ($this->signature?->signature_root);
    }

    public function getHasCartaPoderAttribute(): bool
    {
        return (bool) ($this->signature?->carta_poder_path);
    }

    public function getHasEvidenceAttribute(): bool
    {
        return (bool) ($this->signature?->evidence_root);
    }
}
