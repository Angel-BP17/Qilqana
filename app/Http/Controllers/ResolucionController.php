<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resolucion\CreateResolucionChargeRequest;
use App\Http\Requests\Resolucion\CreateResolucionRequest;
use App\Http\Requests\Resolucion\UpdateResolucionRequest;
use App\Http\Requests\Resolucion\ImportResolucionRequest;
use App\Imports\ResolucionesImport;
use App\Models\Resolucion;
use App\Services\ResolucionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Http\Requests\Resolucion\DeleteResolucionRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResolucionController extends Controller
{
    public function __construct(protected ResolucionService $service)
    {

    }
    public function index(Request $request)
    {
        return view('resolucions.index', $this->service->getAll($request->only(['search', 'periodo'])));
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
        if (!$canCreateCharge) {
            abort(403);
        }

        if ($resolucion->charge) {
            return redirect()->back()->with('info', 'La resolución ya tiene un cargo asociado.');
        }

        $created = $this->service->createCharge($resolucion->id, $user);

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
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        if (!file_exists($templatePath)) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'RD');
            $sheet->setCellValue('B1', 'Fecha');
            $sheet->setCellValue('C1', 'DNI');
            $sheet->setCellValue('D1', 'Nombres y Apellidos');
            $sheet->setCellValue('E1', 'Asunto');
            $sheet->setCellValue('F1', 'Periodo');
            $sheet->setCellValue('G1', 'Procedencia');

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
            'periodo' => $request->periodo
        ];

        return Pdf::loadView('resolucions.report', compact('resoluciones', 'filtros'))
            ->setPaper('a4')
            ->stream('reporte_resoluciones_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        // Usar el filtro unificado
        $query = $this->service->getFilterQuery($request->all());

        // Crear nuevo documento de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = ['RD', 'Fecha', 'Nombres y Apellidos', 'DNI', 'Asunto', 'Periodo', 'Procedencia'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        // Estilos para encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9D9D9']
            ]
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Datos por fragmentos (Chunks) para ahorrar memoria
        $row = 2;
        $query->chunk(200, function($resoluciones) use (&$sheet, &$row) {
            foreach ($resoluciones as $resolucion) {
                $sheet->setCellValue('A' . $row, $resolucion->rd);
                
                $fechaFormateada = $resolucion->fecha ? \Carbon\Carbon::parse($resolucion->fecha)->format('d/m/Y') : '';
                $sheet->setCellValue('B' . $row, $fechaFormateada);

                $sheet->setCellValue('C' . $row, $resolucion->nombres_apellidos);
                $sheet->setCellValue('D' . $row, $resolucion->dni ?? '');
                $sheet->setCellValue('E' . $row, $resolucion->asunto);
                $sheet->setCellValue('F' . $row, $resolucion->periodo);
                $sheet->setCellValue('G' . $row, $resolucion->procedencia);
                $row++;
            }
        });

        // Autoajustar columnas
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = 'resoluciones_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]
        );
    }
}

