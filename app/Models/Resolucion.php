<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Resolucion extends Model
{
    use HasFactory;

    protected $casts = [
        'fecha' => 'datetime',
        'is_worked' => 'boolean',
    ];

    protected $fillable = [
        'resolucion_type_id',
        'asunto_type_id',
        'rd',
        'fecha',
        'periodo',
        'nombres_apellidos',
        'dni',
        'cedula',
        'ruc',
        'razon_social',
        'asunto',
        'procedencia',
        'user_id',
        'level_modality_id',
        'document_path',
        'is_worked',
    ];

    public function type()
    {
        return $this->belongsTo(ResolucionType::class, 'resolucion_type_id');
    }

    public function levelModality(): BelongsTo
    {
        return $this->belongsTo(LevelModality::class, 'level_modality_id');
    }

    public function asuntoType(): BelongsTo
    {
        return $this->belongsTo(AsuntoType::class, 'asunto_type_id');
    }

    public function naturalPeople()
    {
        return $this->morphedByMany(NaturalPerson::class, 'interesado', 'resolucion_interesados');
    }

    public function legalEntities()
    {
        return $this->morphedByMany(LegalEntity::class, 'interesado', 'resolucion_interesados');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'interesado', 'resolucion_interesados');
    }

    /**
     * Sincroniza los campos de texto de la resolución con los datos de sus interesados vinculados.
     */
    public function syncInteresadosData(): void
    {
        $this->load(['naturalPeople', 'legalEntities', 'users']);

        $nombres = [];
        $dnis = [];
        $cedulas = [];
        $rucs = [];
        $razones = [];

        foreach ($this->naturalPeople as $p) {
            $nombres[] = "{$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}";
            if ($p->dni) {
                $dnis[] = $p->dni;
            }
            if ($p->cedula) {
                $cedulas[] = $p->cedula;
            }
        }

        foreach ($this->legalEntities as $e) {
            $razones[] = $e->razon_social;
            $nombres[] = $e->razon_social; // También va en nombres para reportes unificados
            if ($e->ruc) {
                $rucs[] = $e->ruc;
            }
        }

        foreach ($this->users as $u) {
            $nombres[] = "{$u->name} {$u->last_name}";
            if ($u->dni) {
                $dnis[] = $u->dni;
            }
        }

        $this->update([
            'nombres_apellidos' => implode(', ', array_unique($nombres)),
            'dni' => implode(', ', array_unique($dnis)),
            'cedula' => implode(', ', array_unique($cedulas)),
            'ruc' => implode(', ', array_unique($rucs)),
            'razon_social' => implode(', ', array_unique($razones)),
        ]);
    }

    public function charges()
    {
        return $this->belongsToMany(Charge::class);
    }

    public function getChargeAttribute()
    {
        return $this->charges->first();
    }

    /* Accessors para optimizar vistas */

    public function getSignatureStatusAttribute(): ?string
    {
        return $this->charge?->signature?->signature_status;
    }

    public function getSignatureContentAttribute(): ?string
    {
        $signatureRoot = $this->charge?->signature?->signature_root;

        if ($signatureRoot && Storage::disk('local')->exists($signatureRoot)) {
            return Storage::disk('local')->get($signatureRoot);
        }

        return null;
    }

    public function getFormattedFechaAttribute(): string
    {
        return $this->fecha ? $this->fecha->format('d/m/Y') : '---';
    }

    public function getCanSignAttribute(): bool
    {
        return $this->charges()->whereHas('signature', function ($q) {
            $q->where('signature_status', 'pendiente');
        })->exists();
    }

    public function getCanCreateChargeAttribute(): bool
    {
        $totalInteresados = $this->naturalPeople()->count() + $this->legalEntities()->count() + $this->users()->count();

        $cargosActivos = $this->charges()->whereHas('signature', function ($q) {
            $q->where('signature_status', '!=', 'rechazado');
        })->count();

        // Se puede crear cargo si hay al menos un interesado que no tenga cargo activo (no rechazado)
        return $cargosActivos < $totalInteresados;
    }

    /**
     * Obtiene los datos de cargos pendientes de firma para esta resolución.
     */
    public function getPendingChargesDataAttribute(): array
    {
        return $this->charges()
            ->whereHas('signature', function ($q) {
                $q->where('signature_status', 'pendiente');
            })
            ->with(['signature', 'signature.assignedTo'])
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'interesado_name' => $c->interesado_label,
                    'interesado_type' => $c->tipo_interesado,
                    'action_url' => route('charges.sign.store', $c),
                ];
            })
            ->toArray();
    }

    public function getFileDocumentUrlAttribute(): string
    {
        return $this->document_path ? route('resolucions.file.document', $this) : '';
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->with([
            'charges.signature',
            'charges.signature.signer',
            'charges.signature.assignedTo',
            'charges.interesado',
            'type',
            'naturalPeople',
            'legalEntities',
            'users',
            'asuntoType',
            'levelModality',
        ])
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('nombres_apellidos', 'like', "%$search%")
                        ->orWhere('rd', 'like', "%$search%")
                        ->orWhere('asunto', 'like', "%$search%")
                        ->orWhere('procedencia', 'like', "%$search%")
                        ->orWhere('dni', 'like', "%$search%");
                });
            })
            ->when($filters['search_rd'] ?? null, function ($q, $searchRd) {
                $q->where('rd', 'like', "%$searchRd%");
            })
            ->when($filters['search_asunto'] ?? null, function ($q, $searchAsunto) {
                $q->where('asunto', 'like', "%$searchAsunto%");
            })
            ->when($filters['periodo'] ?? null, fn ($q, $periodo) => $q->where('periodo', $periodo))
            ->when($filters['resolucion_type_id'] ?? null, fn ($q, $typeId) => $q->where('resolucion_type_id', $typeId))
            ->when($filters['asunto_type_id'] ?? null, fn ($q, $asuntoId) => $q->where('asunto_type_id', $asuntoId))
            ->when($filters['level_modality_id'] ?? null, fn ($q, $modalityId) => $q->where('level_modality_id', $modalityId))
            ->when($filters['desde'] ?? null, fn ($q, $desde) => $q->whereDate('fecha', '>=', $desde))
            ->when($filters['hasta'] ?? null, fn ($q, $hasta) => $q->whereDate('fecha', '<=', $hasta))
            ->orderByDesc('fecha')
            ->orderByDesc('id');
    }
}
