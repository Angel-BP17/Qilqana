<?php

namespace App\Services\Resolucion;

use App\Filters\ResolucionFilter;
use App\Models\Charge;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Resolucion;
use App\Services\Resolucion\Contracts\ResolucionServiceInterface;
use App\Traits\HasChargeLogic;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ResolucionService implements ResolucionServiceInterface
{
    use HasChargeLogic;

    public function __construct(protected ResolucionFilter $filter) {}

    public function getAll(array $data): array
    {
        if (empty($data['search']) && empty($data['periodo'])) {
            return [
                'resoluciones' => new LengthAwarePaginator([], 0, 20),
                'ultimoRegistro' => Resolucion::latest('id')->value('rd'),
                'periodos' => Resolucion::select('periodo')->distinct()->orderBy('periodo', 'asc')->pluck('periodo'),
                'chargePeriod' => $this->getChargePeriod(),
                'totalResolucionesPeriodo' => 0,
                'pendientesResolucionesPeriodo' => 0,
            ];
        }

        $resoluciones = $this->filter->applyFilters($data)->paginate(20);
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

    public function getFilterQuery(array $filters)
    {
        return $this->filter->applyFilters($filters);
    }

    public function create(array $data): bool
    {
        try {
            $data['periodo'] = Carbon::parse($data['fecha'])->year;
            $data['rd'] = mb_strtoupper(trim($data['rd'] ?? ''), 'UTF-8');

            if (isset($data['procedencia'])) {
                $data['procedencia'] = mb_strtoupper($data['procedencia'], 'UTF-8');
            }
            if (isset($data['asunto'])) {
                $data['asunto'] = mb_strtoupper($data['asunto'], 'UTF-8');
            }

            return DB::transaction(function () use ($data) {
                // 1. Crear Resolución
                $resolucion = Resolucion::create([
                    'resolucion_type_id' => $data['resolucion_type_id'] ?? null,
                    'asunto_type_id' => $data['asunto_type_id'] ?? null,
                    'rd' => $data['rd'],
                    'fecha' => $data['fecha'],
                    'periodo' => $data['periodo'],
                    'asunto' => $data['asunto'],
                    'procedencia' => $data['procedencia'] ?? null,
                    'user_id' => $data['user_id'],
                ]);

                // 2. Vincular/Registrar Interesados
                if (! empty($data['interesados'])) {
                    foreach ($data['interesados'] as $item) {
                        $this->processAndAttachInteresado($resolucion, $item);
                    }
                }

                // 3. Sincronizar campos de texto (Denormalización)
                $resolucion->syncInteresadosData();

                return true;
            });
        } catch (\Throwable $th) {
            report($th);

            return false;
        }
    }

    /**
     * Procesa los datos de un interesado, creándolo si es necesario, y lo vincula a la resolución.
     */
    private function processAndAttachInteresado(Resolucion $resolucion, array $item): void
    {
        $id = $item['id'] ?? null;
        $type = $item['type'];

        // Si es una Persona Natural (nueva o existente)
        if ($type === 'NaturalPerson' || $type === 'Persona Natural') {
            if (! $id) {
                $person = NaturalPerson::updateOrCreate(
                    ['dni' => $item['dni'] ?? null, 'cedula' => $item['cedula'] ?? null],
                    [
                        'nombres' => mb_strtoupper($item['nombres'] ?? '', 'UTF-8'),
                        'apellido_paterno' => mb_strtoupper($item['apellido_paterno'] ?? '', 'UTF-8'),
                        'apellido_materno' => mb_strtoupper($item['apellido_materno'] ?? '', 'UTF-8'),
                    ]
                );
                $id = $person->id;
            }
            $resolucion->naturalPeople()->syncWithoutDetaching([$id]);
        }

        // Si es una Persona Juridica (nueva o existente)
        elseif ($type === 'LegalEntity' || $type === 'Persona Juridica') {
            if (! $id) {
                $entity = LegalEntity::updateOrCreate(
                    ['ruc' => $item['ruc'] ?? null],
                    [
                        'razon_social' => mb_strtoupper($item['razon_social'] ?? '', 'UTF-8'),
                        'district' => mb_strtoupper($item['district'] ?? '', 'UTF-8'),
                    ]
                );
                $id = $entity->id;
            }
            $resolucion->legalEntities()->syncWithoutDetaching([$id]);
        }

        // Si es un Trabajador UGEL (Siempre existente)
        elseif ($type === 'User' || $type === 'Trabajador UGEL') {
            if ($id) {
                $resolucion->users()->syncWithoutDetaching([$id]);
            }
        }
    }

    public function update(array $data, int $id): bool
    {
        try {
            $resolucion = Resolucion::findOrFail($id);

            if (isset($data['fecha'])) {
                $data['periodo'] = Carbon::parse($data['fecha'])->year;
            }
            if (isset($data['rd'])) {
                $data['rd'] = mb_strtoupper(trim($data['rd']), 'UTF-8');
            }
            if (isset($data['procedencia'])) {
                $data['procedencia'] = mb_strtoupper($data['procedencia'], 'UTF-8');
            }
            if (isset($data['asunto'])) {
                $data['asunto'] = mb_strtoupper($data['asunto'], 'UTF-8');
            }

            return DB::transaction(function () use ($data, $resolucion) {
                // 1. Actualizar campos base
                $resolucion->update([
                    'resolucion_type_id' => $data['resolucion_type_id'] ?? $resolucion->resolucion_type_id,
                    'asunto_type_id' => $data['asunto_type_id'] ?? $resolucion->asunto_type_id,
                    'rd' => $data['rd'] ?? $resolucion->rd,
                    'fecha' => $data['fecha'] ?? $resolucion->fecha,
                    'periodo' => $data['periodo'] ?? $resolucion->periodo,
                    'asunto' => $data['asunto'] ?? $resolucion->asunto,
                    'procedencia' => $data['procedencia'] ?? $resolucion->procedencia,
                ]);

                // 2. Sincronizar Interesados (Si se envían)
                if (isset($data['interesados'])) {
                    $resolucion->naturalPeople()->detach();
                    $resolucion->legalEntities()->detach();
                    $resolucion->users()->detach();

                    foreach ($data['interesados'] as $item) {
                        $this->processAndAttachInteresado($resolucion, $item);
                    }
                }

                // 3. Sincronizar campos de texto
                $resolucion->syncInteresadosData();

                return true;
            });
        } catch (\Throwable $th) {
            report($th);

            return false;
        }
    }

    public function generateChargeForResolucion($id, $user): bool
    {
        try {
            $resolucion = Resolucion::with(['naturalPeople', 'legalEntities', 'users'])->findOrFail($id);
            if ($resolucion->charges()->exists()) {
                return true;
            }

            return DB::transaction(function () use ($resolucion, $user) {
                $naturalPersonId = null;
                $legalEntityId = null;
                $assignedTo = $user->id;
                $tipoInteresado = 'Persona Natural';

                $firstNatural = $resolucion->naturalPeople->first();
                $firstLegal = $resolucion->legalEntities->first();
                $firstUser = $resolucion->users->first();

                if ($firstUser) {
                    $assignedTo = $firstUser->id;
                    $tipoInteresado = 'Trabajador UGEL';
                } elseif ($firstLegal) {
                    $legalEntityId = $firstLegal->id;
                    $tipoInteresado = 'Persona Juridica';
                } elseif ($firstNatural) {
                    $naturalPersonId = $firstNatural->id;
                    $tipoInteresado = 'Persona Natural';
                }

                $charge = Charge::create([
                    'n_charge' => $this->nextChargeNumberForUser($user->id),
                    'charge_period' => $this->getChargePeriod(),
                    'document_date' => $resolucion->fecha,
                    'user_id' => $user->id,
                    'tipo_interesado' => $tipoInteresado,
                    'natural_person_id' => $naturalPersonId,
                    'legal_entity_id' => $legalEntityId,
                    'asunto' => $resolucion->asunto,
                ]);

                $charge->resolucions()->attach($resolucion->id);

                $charge->signature()->create([
                    'assigned_to' => $assignedTo,
                    'signature_status' => 'pendiente',
                    'signature_requested_at' => now(),
                ]);

                return true;
            });
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

    private function getStats(?string $chargePeriod): array
    {
        if (! $chargePeriod) {
            return ['totalResolucionesPeriodo' => null, 'pendientesResolucionesPeriodo' => null];
        }

        return [
            'totalResolucionesPeriodo' => Resolucion::where('periodo', $chargePeriod)->count(),
            'pendientesResolucionesPeriodo' => Charge::whereHas('resolucions', fn ($q) => $q->where('periodo', $chargePeriod))
                ->where('charge_period', $chargePeriod)
                ->whereHas('signature', fn ($q) => $q->where('signature_status', 'pendiente'))
                ->count(),
        ];
    }
}
