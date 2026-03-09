<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use DB;
use Maatwebsite\Excel\Concerns\{
    WithStartRow,
    WithChunkReading,
    WithBatchInserts,
    WithCalculatedFormulas
// Removido WithHeadingRow ya que no se usa
};

class ResolucionesImport implements
    ToCollection,
    WithStartRow,
    WithChunkReading,
    WithBatchInserts,
    WithCalculatedFormulas,
    ShouldQueue
{
    private $startRow = 2;
    private $startColumn = 'A';
    private $columnIndex = 0; // Índice numérico para la columna de inicio

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
        $buffer = [];

        foreach ($rows as $row) {
            // Convertir a array y obtener solo las columnas necesarias
            $rowData = $row->toArray();

            // Si la fila está vacía, saltar
            if (empty(array_filter($rowData))) {
                continue;
            }

            // Obtener valores comenzando desde la columna especificada
            $rd = $rowData[$this->columnIndex] ?? '';
            $fecha = $this->parseFecha($rowData[$this->columnIndex + 1] ?? null);
            $dni = $rowData[$this->columnIndex + 2] ?? '';
            $nombres = $rowData[$this->columnIndex + 3] ?? '';
            $asunto = $rowData[$this->columnIndex + 4] ?? '';
            $periodo = $rowData[$this->columnIndex + 5] ?? ($fecha ? $fecha->year : null);
            $procedencia = $rowData[$this->columnIndex + 6] ?? '';

            // Validar RD obligatorio
            if (empty(trim($rd))) {
                continue;
            }

            $buffer[] = [
                'rd' => trim($rd),
                'fecha' => $fecha ? $fecha->toDateString() : null,
                'dni' => ltrim($dni, "'"),
                'nombres_apellidos' => trim($nombres),
                'asunto' => trim($asunto),
                'procedencia' => trim($procedencia),
                'periodo' => $periodo,
            ];
        }

        // Guardar en bloque si hay datos
        if (!empty($buffer)) {
            DB::table('resolucions')->upsert($buffer, ['rd']);
        }
    }

    protected function parseFecha($valor): ?Carbon
    {
        if (blank($valor)) {
            return null;
        }

        // Número de serie de Excel
        if (is_numeric($valor)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($valor));
        }

        // Manejar diferentes formatos de fecha
        $formats = [
            'd/m/Y',
            'd-m-Y',
            'Y-m-d',
            'm/d/Y',
            'd.m.Y',
            'Y.m.d',
            'd M Y',
            'd F Y'
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($valor));
            } catch (\Exception $e) {
                // Continuar intentando
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
        return 2000;
    }

    public function batchSize(): int
    {
        return 2000;
    }
}