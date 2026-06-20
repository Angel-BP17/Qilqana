<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ResolucionesImport implements ShouldQueue, ToCollection, WithCalculatedFormulas, WithChunkReading, WithStartRow
{
    private $startRow = 2;

    private $startColumn = 'A';

    private $columnIndex = 0;

    public function setStartRow(int $row): void
    {
        $this->startRow = $row;
    }

    public function setStartColumn(string $column): void
    {
        $this->startColumn = strtoupper($column);
        $this->columnIndex = ord($this->startColumn) - 65;
    }

    public function collection(Collection $rows): void
    {
        // Intentar forzar límites en hosting compartido (si el host lo permite)
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $resolucionesBuffer = [];
        $personasBuffer = [];
        $dnisParaValidar = [];

        foreach ($rows as $row) {
            $rowData = $row->toArray();
            if (empty(array_filter($rowData))) {
                continue;
            }

            $rd = trim($rowData[$this->columnIndex] ?? '');
            if (empty($rd)) {
                continue;
            }

            $fecha = $this->parseFecha($rowData[$this->columnIndex + 1] ?? null);
            $rawDni = trim($rowData[$this->columnIndex + 2] ?? '');
            $dni = ($rawDni === 'NULL' || empty($rawDni)) ? null : ltrim($rawDni, "'");
            $ruc = trim($rowData[$this->columnIndex + 3] ?? null);
            $nombresCsv = trim($rowData[$this->columnIndex + 4] ?? 'SIN NOMBRE');
            $asunto = trim($rowData[$this->columnIndex + 5] ?? 'Sin asunto');
            $periodo = $rowData[$this->columnIndex + 6] ?? ($fecha ? $fecha->year : null);
            $procedencia = trim($rowData[$this->columnIndex + 7] ?? 'IMPORTACIÓN MASIVA');
            $typeId = $rowData[$this->columnIndex + 8] ?? null;

            $resolucionesBuffer[] = [
                'rd' => $rd,
                'periodo' => $periodo,
                'fecha' => $fecha ? $fecha->toDateTimeString() : null,
                'dni' => $dni,
                'ruc' => $ruc,
                'resolucion_type_id' => $typeId,
                'nombres_apellidos' => $nombresCsv,
                'asunto' => $asunto,
                'procedencia' => $procedencia,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($dni && preg_match('/^\d{8,10}$/', $dni)) {
                $dnisParaValidar[] = $dni;
                $personasBuffer[$dni] = [
                    'dni' => $dni,
                    'nombres' => $nombresCsv,
                    'apellido_paterno' => null,
                    'apellido_materno' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // En hosting compartido usamos chunks más pequeños (500) para no saturar MySQL
        if (! empty($resolucionesBuffer)) {
            // Usar DB::table directamente es más ligero que el modelo en procesos masivos
            DB::table('resolucions')->upsert($resolucionesBuffer, ['rd', 'periodo'], ['fecha', 'dni', 'ruc', 'resolucion_type_id', 'nombres_apellidos', 'asunto', 'procedencia', 'updated_at']);
        }

        if (! empty($personasBuffer)) {
            $dnisExistentes = DB::table('natural_people')->whereIn('dni', $dnisParaValidar)->pluck('dni')->toArray();
            $nuevasPersonas = array_diff_key($personasBuffer, array_flip($dnisExistentes));

            if (! empty($nuevasPersonas)) {
                DB::table('natural_people')->insert(array_values($nuevasPersonas));
            }
        }
    }

    protected function parseFecha($valor): ?Carbon
    {
        if (blank($valor)) {
            return null;
        }
        if (is_numeric($valor)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($valor));
        }
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($valor));
            } catch (\Exception $e) {
            }
        }

        return null;
    }

    public function startRow(): int
    {
        return $this->startRow;
    }

    public function chunkSize(): int
    {
        return 500;
    } // Chunks más pequeños para hostings débiles

    public function batchSize(): int
    {
        return 500;
    }
}
