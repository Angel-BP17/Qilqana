<?php

namespace App\Services\Charge;

use App\Filters\ChargeFilter;
use App\Models\Charge;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ChargeReportService
{
    public function __construct(protected ChargeFilter $filter) {}

    public function generateReport(string $title, string $type, $charges, array $filters): Response
    {
        return Pdf::loadView('charges.report', [
            'title' => $title,
            'type' => $type,
            'charges' => $charges,
            'filters' => $filters,
        ])->setPaper('a4')
            ->stream("reporte_cargos_{$type}_".now()->format('Ymd_His').'.pdf');
    }

    public function getSentReport(array $data, User $user, string $defaultPeriod): Response
    {
        $filters = $this->prepareFilters($data, $defaultPeriod);
        $charges = $this->filter->getAllSentCharges($filters, $user);

        return $this->generateReport('REPORTE DE CARGOS ENVIADOS', 'sent', $charges, $filters);
    }

    public function getCreatedReport(array $data, User $user, string $defaultPeriod): Response
    {
        $filters = $this->prepareFilters($data, $defaultPeriod);
        $charges = $this->filter->getAllCreatedCharges($filters, $user);

        return $this->generateReport('REPORTE DE CARGOS CREADOS', 'created', $charges, $filters);
    }

    public function getResolutionReport(array $data, User $user, string $defaultPeriod): Response
    {
        $search = $data['search'] ?? null;
        $period = $data['period'] ?? $defaultPeriod;

        $charges = Charge::with(['resolucion', 'signature', 'naturalPerson', 'legalEntity'])
            ->whereNotNull('resolucion_id')
            ->whereHas('signature', fn ($q) => $q->where('signature_status', 'firmado'))
            ->when($period, fn ($q) => $q->where('charge_period', $period))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('n_charge', 'like', "%{$search}%")
                        ->orWhereHas('resolucion', function ($resolucion) use ($search) {
                            $resolucion->where('rd', 'like', "%{$search}%")
                                ->orWhere('nombres_apellidos', 'like', "%{$search}%")
                                ->orWhere('dni', 'like', "%{$search}%")
                                ->orWhere('asunto', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return $this->generateReport('REPORTE DE CARGOS DE RESOLUCIONES', 'resolution', $charges, compact('search', 'period'));
    }

    public function getReceivedReport(array $data, User $user, string $defaultPeriod): Response
    {
        $filters = $this->prepareFilters($data, $defaultPeriod);
        $filters['signature_status'] = $data['signature_status'] ?? null;

        $charges = $this->filter->getAllReceivedCharges($filters, $user);

        return $this->generateReport('REPORTE DE CARGOS RECIBIDOS', 'received', $charges, $filters);
    }

    private function prepareFilters(array $data, string $defaultPeriod): array
    {
        return [
            'search' => $data['search'] ?? null,
            'signature_status' => $data['signature_status'] ?? null,
            'period' => $data['period'] ?? $defaultPeriod,
        ];
    }
}
