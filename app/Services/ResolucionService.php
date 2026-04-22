<?php

namespace App\Services;

use App\Filters\ResolucionFilter;
use App\Models\Charge;
use App\Models\NaturalPerson;
use App\Models\Resolucion;
use App\Models\Setting;
use App\Services\Contracts\ResolucionServiceInterface;
use App\Traits\HasChargeLogic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ResolucionService implements ResolucionServiceInterface
{
    use HasChargeLogic;

    public function __construct(protected ResolucionFilter $filter)
    {
    }

    public function getAll(array $data): array
    {
        if (empty($data['search']) && empty($data['periodo'])) {
            return [
                'resoluciones' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'ultimoRegistro' => Resolucion::latest('id')->value('rd'),
                'periodos' => Resolucion::select('periodo')->distinct()->orderBy('periodo', 'asc')->pluck('periodo'),
                'chargePeriod' => $this->getChargePeriod(),
                'totalResolucionesPeriodo' => 0,
                'pendientesResolucionesPeriodo' => 0
            ];
        }

        $resoluciones = $this->getFilterQuery($data)->paginate(20);
        $ultimoRegistro = Resolucion::latest('id')->value('rd');
        
        $periodos = Resolucion::select('periodo')
            ->distinct()
            ->orderBy('periodo', 'asc')
            ->pluck('periodo');

        $chargePeriod = $this->getChargePeriod();
        $stats = $this->getStats($chargePeriod);

        return array_merge(
            compact('resoluciones', 'ultimoRegistro', 'periodos', 'chargePeriod'),
            $stats
        );
    }

    /**
     * Devuelve la consulta filtrada (útil para exportaciones y dashboard)
     */
    public function getFilterQuery(array $filters)
    {
        return $this->filter->applyFilters($filters);
    }

    public function create(array $data): bool
    {
        try {
            $data['periodo'] = Carbon::parse($data['fecha'])->year;

            return DB::transaction(function () use ($data) {
                // 1. Registrar o recuperar Persona Natural
                $personId = null;
                if (!empty($data['dni'])) {
                    $person = NaturalPerson::updateOrCreate(
                        ['dni' => $data['dni']],
                        [
                            'nombres' => $data['nombres'] ?? '',
                            'apellido_paterno' => $data['apellido_paterno'] ?? '',
                            'apellido_materno' => $data['apellido_materno'] ?? '',
                        ]
                    );
                    $personId = $person->id;
                }

                // 2. Concatenar para la tabla resolucions (compatibilidad)
                if (!isset($data['nombres_apellidos'])) {
                    $data['nombres_apellidos'] = trim(
                        ($data['nombres'] ?? '') . ' ' . 
                        ($data['apellido_paterno'] ?? '') . ' ' . 
                        ($data['apellido_materno'] ?? '')
                    );
                }

                // 3. Crear Resolución
                $resolucion = Resolucion::create($data);

                // 4. Crear Cargo vinculado a la persona
                $this->createChargeForResolucion($resolucion, $data['user_id'], $personId);
                return true;
            });
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function createCharge($id, $user): bool
    {
        try {
            $resolucion = Resolucion::findOrFail($id);
            if ($resolucion->charge) return true;

            return DB::transaction(function () use ($resolucion, $user) {
                // Buscar a la persona por el DNI registrado en la resolución
                $person = NaturalPerson::where('dni', $resolucion->dni)->first();
                
                $this->createChargeForResolucion($resolucion, $user->id, $person?->id);
                return true;
            });
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function update(array $data, int $id): bool
    {
        try {
            $model = Resolucion::findOrFail($id);
            if (isset($data['fecha'])) $data['periodo'] = Carbon::parse($data['fecha'])->year;

            // Actualizar o crear Persona Natural si viene DNI
            if (!empty($data['dni'])) {
                NaturalPerson::updateOrCreate(
                    ['dni' => $data['dni']],
                    [
                        'nombres' => $data['nombres'] ?? '',
                        'apellido_paterno' => $data['apellido_paterno'] ?? '',
                        'apellido_materno' => $data['apellido_materno'] ?? '',
                    ]
                );
            }

            // Concatenar si vienen los campos segregados
            if (isset($data['nombres'])) {
                $data['nombres_apellidos'] = trim(
                    ($data['nombres'] ?? '') . ' ' . 
                    ($data['apellido_paterno'] ?? '') . ' ' . 
                    ($data['apellido_materno'] ?? '')
                );
            }

            return (bool) $model->update($data);
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function delete(array $data, int $id): bool
    {
        try {
            $model = Resolucion::findOrFail($id);
            return (bool) $model->delete();
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    private function createChargeForResolucion(Resolucion $resolucion, int $userId, ?int $naturalPersonId = null): void
    {
        $charge = Charge::create([
            'n_charge' => $this->nextChargeNumberForUser($userId),
            'charge_period' => $this->getChargePeriod(),
            'user_id' => $userId,
            'resolucion_id' => $resolucion->id,
            'natural_person_id' => $naturalPersonId, // Vínculo crítico corregido
            'asunto' => $resolucion->asunto,
            'tipo_interesado' => 'Persona Natural',
        ]);

        $charge->signature()->create([
            'assigned_to' => $userId,
            'signature_status' => 'pendiente',
            'signature_requested_at' => now(),
        ]);
    }

    private function getStats(?string $chargePeriod): array
    {
        if (!$chargePeriod) return ['totalResolucionesPeriodo' => null, 'pendientesResolucionesPeriodo' => null];

        return [
            'totalResolucionesPeriodo' => Resolucion::where('periodo', $chargePeriod)->count(),
            'pendientesResolucionesPeriodo' => Charge::whereNotNull('resolucion_id')
                ->where('charge_period', $chargePeriod)
                ->whereHas('signature', fn($q) => $q->where('signature_status', 'pendiente'))
                ->count()
        ];
    }
}
