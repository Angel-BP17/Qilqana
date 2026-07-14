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
        'interesado_type',
        'interesado_id',
        'asunto',
        'document_path',
    ];

    protected $appends = [
        'tipo_interesado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolucions()
    {
        return $this->belongsToMany(Resolucion::class);
    }

    public function getResolucionAttribute()
    {
        return $this->resolucions->first();
    }

    public function interesado()
    {
        return $this->morphTo();
    }

    /**
     * Obtener el representante a través de la entidad legal.
     */
    public function getRepresentativeAttribute()
    {
        return ($this->interesado instanceof LegalEntity) ? $this->interesado->representative : null;
    }

    public function signature()
    {
        return $this->hasOne(Signature::class);
    }

    /* Accessors para optimizar vistas */

    public function getTipoInteresadoAttribute(): string
    {
        return match ($this->interesado_type) {
            NaturalPerson::class => 'Persona Natural',
            LegalEntity::class => 'Persona Juridica',
            User::class => 'Trabajador UGEL',
            default => 'Desconocido',
        };
    }

    public function getInteresadoLabelAttribute(): string
    {
        if (! $this->interesado) {
            return '---';
        }

        if ($this->interesado instanceof User) {
            $targetUser = $this->signature?->assignedTo;

            return $targetUser ? trim(($targetUser->name ?? '').' '.($targetUser->last_name ?? '')) : '---';
        }

        if ($this->interesado instanceof LegalEntity) {
            return $this->interesado->razon_social ?: $this->interesado->ruc ?: '---';
        }

        if ($this->interesado instanceof NaturalPerson) {
            $fullName = trim(($this->interesado->nombres ?? '').' '.($this->interesado->apellido_paterno ?? '').' '.($this->interesado->apellido_materno ?? ''));

            return $fullName !== '' ? $fullName : ($this->interesado->dni ?? '---');
        }

        return '---';
    }

    public function getInteresadoDniAttribute(): string
    {
        if (! $this->interesado) {
            return '---';
        }

        if ($this->interesado instanceof NaturalPerson) {
            return $this->interesado->dni ?: $this->interesado->cedula ?: '---';
        }

        if ($this->interesado instanceof LegalEntity) {
            return $this->interesado->ruc ?: '---';
        }

        if ($this->interesado instanceof User) {
            return $this->interesado->dni ?: '---';
        }

        return '---';
    }

    public function getHasSignatureAttribute(): bool
    {
        return (bool) ($this->signature?->signature_root);
    }

    public function getSignatureContentAttribute(): ?string
    {
        $signatureRoot = $this->signature?->signature_root;

        if ($signatureRoot && \Storage::disk('local')->exists($signatureRoot)) {
            return \Storage::disk('local')->get($signatureRoot);
        }

        return null;
    }

    public function getHasCartaPoderAttribute(): bool
    {
        return (bool) ($this->signature?->carta_poder_path);
    }

    public function getHasEvidenceAttribute(): bool
    {
        return (bool) ($this->signature?->evidence_root);
    }

    public function getFileSignatureUrlAttribute(): string
    {
        return $this->has_signature ? route('charges.file.signature', $this) : '';
    }

    public function getFileEvidenceUrlAttribute(): string
    {
        return $this->has_evidence ? route('charges.file.evidence', $this) : '';
    }

    public function getFileCartaPoderUrlAttribute(): string
    {
        return $this->has_carta_poder ? route('charges.file.carta-poder', $this) : '';
    }

    public function getFileDocumentUrlAttribute(): string
    {
        return $this->document_path ? route('charges.file.document', $this) : '';
    }

    public function scopeFilterByPeriod(\Illuminate\Database\Eloquent\Builder $query, ?string $period): \Illuminate\Database\Eloquent\Builder
    {
        return $query->when($period, function ($q, $period) {
            $q->where(function ($q2) use ($period) {
                $q2->where('charge_period', $period)->orWhereNull('charge_period');
            });
        });
    }

    public function scopeFilterBySignatureStatus(\Illuminate\Database\Eloquent\Builder $query, ?string $status): \Illuminate\Database\Eloquent\Builder
    {
        return $query->when(in_array($status, ['pendiente', 'firmado', 'rechazado'], true), function ($q) use ($status) {
            $q->whereHas('signature', fn ($s) => $s->where('signature_status', $status));
        });
    }

    public function scopeSearch(\Illuminate\Database\Eloquent\Builder $query, ?string $search): \Illuminate\Database\Eloquent\Builder
    {
        return $query->when($search, function ($q, $search) {
            $q->where(function ($q2) use ($search) {
                $q2->where('n_charge', 'like', "%{$search}%")
                    ->orWhere('asunto', 'like', "%{$search}%")
                    ->orWhereHasMorph('interesado', [NaturalPerson::class, LegalEntity::class], function ($morphQuery, $type) use ($search) {
                        if ($type === NaturalPerson::class) {
                            $morphQuery->where('nombres', 'like', "%{$search}%")
                                ->orWhere('apellido_paterno', 'like', "%{$search}%")
                                ->orWhere('apellido_materno', 'like', "%{$search}%")
                                ->orWhere('dni', 'like', "%{$search}%");
                        } elseif ($type === LegalEntity::class) {
                            $morphQuery->where('razon_social', 'like', "%{$search}%")
                                ->orWhere('ruc', 'like', "%{$search}%")
                                ->orWhere('district', 'like', "%{$search}%");
                        }
                    });
            });
        });
    }
}
