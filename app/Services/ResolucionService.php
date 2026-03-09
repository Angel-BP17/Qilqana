<?php
namespace App\Services;

use App\Filters\ResolucionFilter;
use App\Models\Charge;
use App\Models\Resolucion;
use App\Models\Setting;
use App\Services\Contracts\ResolucionServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ResolucionService implements ResolucionServiceInterface
{
    public function __construct(protected ResolucionFilter $filter)
    {
    }
    public function getAll(array $data): array
    {
        $resoluciones = $this->filter->applyFilters($data)->paginate(20);

        $ultimoRegistro = Resolucion::latest('id')->value('rd');

        $ultimoPeriodo = Resolucion::latest('id')
            ->whereNotNull('periodo')
            ->value('periodo');

        // Obtener periodos únicos para el filtro
        $periodos = Resolucion::select('periodo')
            ->distinct()
            ->orderBy('periodo', 'asc')
            ->pluck('periodo');

        $chargePeriod = Setting::getValue('charge_period', '');

        $totalResolucionesPeriodo = null;
        $pendientesResolucionesPeriodo = null;
        if ($chargePeriod !== '') {
            $totalResolucionesPeriodo = Resolucion::where('periodo', $chargePeriod)->count();
            $pendientesResolucionesPeriodo = Charge::whereNotNull('resolucion_id')
                ->where('charge_period', $chargePeriod)
                ->whereHas('signature', function ($q) {
                    $q->where('signature_status', 'pendiente');
                })
                ->count();
        }

        return compact(
            'resoluciones',
            'ultimoRegistro',
            'periodos',
            'chargePeriod',
            'totalResolucionesPeriodo',
            'pendientesResolucionesPeriodo'
        );
    }

    public function find(array $criteria): array
    {
        throw new \Exception('Not implemented');
    }

    public function getById(int $id): array
    {
        throw new \Exception('Not implemented');
    }

    public function create(array $data): bool
    {
        try {
            $data['periodo'] = Carbon::parse($data['fecha'])->year;

            DB::transaction(function () use ($data) {

                // Registrar la resolución
                $resolucion = Resolucion::create($data);

                // Crear el cargo asociado
                $charge = Charge::create([
                    'n_charge' => $this->nextChargeNumberForUser($data['user_id']),
                    'charge_period' => $this->getChargePeriod(),
                    'user_id' => $data['user_id'],
                    'resolucion_id' => $resolucion->id,
                    'asunto' => $resolucion->asunto,
                    'tipo_interesado' => 'Persona Natural',
                ]);

                // Crear la firma asociada al cargo
                $charge->signature()->create([
                    'assigned_to' => $data['user_id'],
                    'signature_status' => 'pendiente',
                    'signature_requested_at' => now(),
                ]);
            });

            return true;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function createCharge($id, $user)
    {
        try {
            $resolucion = Resolucion::findOrFail($id);

            DB::transaction(function () use ($resolucion, $user) {
                $charge = Charge::create([
                    'n_charge' => $this->nextChargeNumberForUser($user->id),
                    'charge_period' => $this->getChargePeriod(),
                    'user_id' => $user->id,
                    'resolucion_id' => $resolucion->id,
                    'asunto' => $resolucion->asunto,
                    'tipo_interesado' => 'Persona Natural',
                ]);

                $charge->signature()->create([
                    'assigned_to' => $user->id,
                    'signature_status' => 'pendiente',
                    'signature_requested_at' => now(),
                ]);
            });

            return true;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function update(array $data, int $id): bool
    {
        try {
            $model = Resolucion::findOrFail($id);

            $data['periodo'] = Carbon::parse($data['fecha'])->year;
            DB::transaction(function () use ($data, $model) {
                $model->update($data);
            });

            return true;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function delete(array $data, int $id): bool
    {
        try {
            $model = Resolucion::findOrFail($id);

            DB::transaction(function () use ($model) {
                $model->delete();
            });

            return true;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    private function nextChargeNumberForUser(?int $userId): string
    {
        if (!$userId) {
            return '1';
        }

        $period = $this->getChargePeriod();
        $query = Charge::where('user_id', $userId);
        if ($period) {
            $query->where('charge_period', $period);
        } else {
            $query->whereNull('charge_period');
        }
        $maxValue = $query->max(DB::raw('CAST(n_charge as UNSIGNED)'));
        $nextValue = ((int) $maxValue) + 1;

        return (string) $nextValue;
    }

    private function getChargePeriod(): ?string
    {
        $period = Setting::getValue('charge_period', '');
        return $period !== '' ? $period : null;
    }
}
