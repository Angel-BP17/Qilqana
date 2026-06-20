<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resolucion extends Model
{
    use HasFactory;

    protected $casts = [
        'fecha' => 'datetime',
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
    ];

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
        return $this->charge && $this->signature_status === 'pendiente';
    }

    public function getCanCreateChargeAttribute(): bool
    {
        // Se puede crear cargo solo si no tiene ninguno que NO esté rechazado
        return ! $this->charges()->whereHas('signature', function ($q) {
            $q->where('signature_status', '!=', 'rechazado');
        })->exists();
    }
}
