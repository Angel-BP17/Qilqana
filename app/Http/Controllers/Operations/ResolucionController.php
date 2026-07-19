<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resolucion\CreateResolucionChargeRequest;
use App\Http\Requests\Resolucion\CreateResolucionRequest;
use App\Http\Requests\Resolucion\DeleteResolucionRequest;
use App\Http\Requests\Resolucion\ImportResolucionRequest;
use App\Http\Requests\Resolucion\UpdateResolucionRequest;
use App\Imports\ResolucionesImport;
use App\Models\LegalEntity;
use App\Models\LevelModality;
use App\Models\NaturalPerson;
use App\Models\Resolucion;
use App\Models\ResolucionType;
use App\Models\User;
use App\Services\Resolucion\ResolucionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResolucionController extends Controller
{
    public function __construct(protected ResolucionService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->getAll($request->only(['search', 'search_rd', 'search_asunto', 'periodo', 'resolucion_type_id', 'asunto_type_id', 'level_modality_id', 'desde', 'hasta']));
        $data['types'] = ResolucionType::orderBy('name')->get();
        $data['users'] = User::orderBy('name')->get();
        $data['level_modalities'] = LevelModality::orderBy('name')->get();

        return view('resolucions.index', $data);
    }

    public function store(CreateResolucionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()?->id;

        $this->service->create($data);

        return redirect()->back()->with('success', 'La resolución se guardó con éxito.');
    }

    public function createCharge(CreateResolucionChargeRequest $request, Resolucion $resolucion)
    {
        $user = $request->user();

        $canCreateCharge = $user?->hasRole('ADMINISTRADOR') || $user?->can('modulo resoluciones');
        if (! $canCreateCharge) {
            abort(403);
        }

        $interesadoId = $request->input('interesado_id');
        $interesadoType = $request->input('interesado_type');

        if ($interesadoId && $interesadoType) {
            // Mapear tipo amigable a clase si viene en formato texto
            $typeMap = [
                'Persona Natural' => NaturalPerson::class,
                'Persona Juridica' => LegalEntity::class,
                'Trabajador UGEL' => User::class,
                'App\Models\NaturalPerson' => NaturalPerson::class,
                'App\Models\LegalEntity' => LegalEntity::class,
                'App\Models\User' => User::class,
            ];
            $interesadoClass = $typeMap[$interesadoType] ?? $interesadoType;

            // Verificar si este interesado ya tiene un cargo activo para esta resolución
            $hasChargeForInteresado = $resolucion->charges()
                ->where('interesado_type', $interesadoClass)
                ->where('interesado_id', $interesadoId)
                ->whereHas('signature', function ($q) {
                    $q->where('signature_status', '!=', 'rechazado');
                })
                ->exists();

            if ($hasChargeForInteresado) {
                return redirect()->back()->with('info', 'El interesado seleccionado ya tiene un cargo activo asociado en esta resolución.');
            }
        } else {
            // Si no se especifica interesado, se verifica si todos ya tienen cargo activo
            $totalInteresados = $resolucion->naturalPeople()->count() + $resolucion->legalEntities()->count() + $resolucion->users()->count();
            $cargosActivos = $resolucion->charges()->whereHas('signature', function ($q) {
                $q->where('signature_status', '!=', 'rechazado');
            })->count();

            if ($cargosActivos >= $totalInteresados) {
                return redirect()->back()->with('info', 'Todos los interesados de esta resolución ya tienen cargos activos asociados.');
            }
        }

        $created = $this->service->generateChargeForResolucion($resolucion->id, $user, $interesadoId, $interesadoType);

        if ($created) {
            return redirect()->back()->with('success', 'Cargo creado para la resolución.');
        }

        return redirect()->back()->with('error', 'Ocurrió un error al crear el cargo. Por favor, intente nuevamente.');
    }

    public function update(UpdateResolucionRequest $request, Resolucion $resolucion)
    {
        $this->service->update($request->validated(), $resolucion->id);

        return redirect()->back()->with('success', 'La resolución se actualizó con éxito');
    }

    public function destroy(DeleteResolucionRequest $request, Resolucion $resolucion)
    {
        $data = $request->validated();

        $this->service->delete($data, $resolucion->id);

        return redirect()->back()->with('success', 'La resolución se eliminó exitosamente');
    }

    public function import(ImportResolucionRequest $request)
    {
        // En segundo plano   (cola ‘imports’ opcional)
        Excel::queueImport(new ResolucionesImport, $request->file('archivo_excel'))
            ->allOnQueue('imports');

        return back()->with('ok', 'El archivo se está procesando; te avisaremos al terminar.');
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $templatePath = storage_path('app/public/templates/Plantilla_Resoluciones.xlsx');
        $templateDir = dirname($templatePath);
        if (! is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        if (! file_exists($templatePath)) {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'RD');
            $sheet->setCellValue('B1', 'Fecha');
            $sheet->setCellValue('C1', 'DNI');
            $sheet->setCellValue('D1', 'RUC');
            $sheet->setCellValue('E1', 'Nombres y Apellidos');
            $sheet->setCellValue('F1', 'Asunto');
            $sheet->setCellValue('G1', 'Periodo');
            $sheet->setCellValue('H1', 'Procedencia');
            $sheet->setCellValue('I1', 'Tipo (ID)');

            $writer = new Xlsx($spreadsheet);
            $writer->save($templatePath);
        }

        return response()->download($templatePath, 'Plantilla_Importacion_Resoluciones.xlsx');
    }

    public function generatePDF(Request $request)
    {
        // Usar el filtro unificado para garantizar consistencia
        $query = $this->service->getFilterQuery($request->all());

        // Limitar a 500 registros para PDF en hosting compartido (evita error de memoria)
        $resoluciones = $query->limit(500)->get();

        $filtros = [
            'search' => $request->search,
            'search_rd' => $request->search_rd,
            'search_asunto' => $request->search_asunto,
            'periodo' => $request->periodo,
        ];

        return Pdf::loadView('resolucions.report', compact('resoluciones', 'filtros'))
            ->setPaper('a4')
            ->stream('reporte_resoluciones_'.now()->format('Ymd_His').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        // Usar el filtro unificado
        $query = $this->service->getFilterQuery($request->all());

        // Crear nuevo documento de Excel
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados: ID - RD - FECHA - NOMBREYAPELLIDO - DNI O RUC - TIPO DE ASUNTO - NIVEL - PERIODO - FIRMA
        $headers = ['ID', 'RD', 'FECHA', 'NOMBREYAPELLIDO', 'DNI O RUC', 'TIPO DE ASUNTO', 'NIVEL', 'PERIODO', 'FIRMA'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        // Estilos para encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9D9D9'],
            ],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Datos por fragmentos (Chunks) para ahorrar memoria
        $row = 2;
        $query->chunk(200, function ($resoluciones) use (&$sheet, &$row) {
            foreach ($resoluciones as $resolucion) {
                $sheet->setCellValue('A'.$row, $resolucion->id);
                $sheet->setCellValue('B'.$row, $resolucion->rd);

                $fechaFormateada = $resolucion->fecha ? Carbon::parse($resolucion->fecha)->format('d/m/Y') : '';
                $sheet->setCellValue('C'.$row, $fechaFormateada);

                $sheet->setCellValue('D'.$row, $resolucion->nombres_apellidos);

                // Unificar DNI y RUC de la resolución
                $dniList = array_filter(explode(', ', $resolucion->dni ?? ''));
                $rucList = array_filter(explode(', ', $resolucion->ruc ?? ''));
                $combined = array_merge($dniList, $rucList);
                $dniOrRucVal = implode(', ', $combined);
                $sheet->setCellValue('E'.$row, $dniOrRucVal);

                $sheet->setCellValue('F'.$row, $resolucion->asuntoType?->name ?? '---');
                $sheet->setCellValue('G'.$row, $resolucion->levelModality?->name ?? '---');
                $sheet->setCellValue('H'.$row, $resolucion->periodo);

                $status = $resolucion->signature_status;
                $firmaText = $status === 'firmado' ? 'FIRMADO' : ($status === 'rechazado' ? 'RECHAZADO' : 'PENDIENTE');
                $sheet->setCellValue('I'.$row, $firmaText);

                $row++;
            }
        });

        // Autoajustar columnas
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = 'resoluciones_'.now()->format('Ymd_His').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ]
        );
    }

    public function getDocument(Resolucion $resolucion)
    {
        if (! $resolucion->document_path) {
            abort(404);
        }

        $path = $resolucion->document_path;
        if (! \Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(\Storage::disk('local')->path($path));
    }

    public function markAsWorked(Resolucion $resolucion)
    {
        if (! auth()->user()->can('resolucion marcar trabajada')) {
            abort(403, 'No tiene permiso para realizar esta acción.');
        }

        $resolucion->update(['is_worked' => true]);

        return redirect()->back()->with('success', 'Resolución marcada como trabajada correctamente.');
    }
}
