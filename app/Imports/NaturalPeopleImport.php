<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithStartRow,
    WithChunkReading,
    WithBatchInserts,
    WithCalculatedFormulas
};

class NaturalPeopleImport implements
    ToCollection,
    WithStartRow,
    WithChunkReading,
    WithBatchInserts,
    WithCalculatedFormulas
{
    public function __construct(private bool $updateExisting = true)
    {
    }

    private bool $headerChecked = false;
    private array $columnMap = [
        'dni' => 0,
        'nombres' => 1,
        'apellido_paterno' => 2,
        'apellido_materno' => 3,
    ];

    public function collection(Collection $rows): void
    {
        if (!$this->headerChecked) {
            $firstRow = $rows->first();
            if ($firstRow) {
                $headerMap = $this->detectHeaderMap($firstRow->toArray());
                if (!empty($headerMap)) {
                    $this->columnMap = array_merge($this->columnMap, $headerMap);
                    $rows = $rows->slice(1);
                }
            }
            $this->headerChecked = true;
        }

        $buffer = [];
        $now = now();

        foreach ($rows as $row) {
            $rowData = $row->toArray();
            if (empty(array_filter($rowData))) {
                continue;
            }

            $dni = $this->valueAt($rowData, 'dni');
            $nombres = $this->valueAt($rowData, 'nombres');
            $apellidoPaterno = $this->valueAt($rowData, 'apellido_paterno');
            $apellidoMaterno = $this->valueAt($rowData, 'apellido_materno');

            if ($apellidoPaterno === '' && isset($this->columnMap['apellidos'])) {
                $apellidoPaterno = $this->valueAt($rowData, 'apellidos');
            }

            if ($apellidoMaterno === '' && isset($this->columnMap['apellidos'])) {
                $parts = preg_split('/\s+/', trim($apellidoPaterno), 2);
                if (count($parts) === 2) {
                    $apellidoPaterno = $parts[0];
                    $apellidoMaterno = $parts[1];
                }
            }

            $dni = ltrim($dni, "'");
            if ($dni === '') {
                continue;
            }

            $buffer[] = [
                'dni' => $dni,
                'nombres' => $nombres !== '' ? $nombres : null,
                'apellido_paterno' => $apellidoPaterno !== '' ? $apellidoPaterno : null,
                'apellido_materno' => $apellidoMaterno !== '' ? $apellidoMaterno : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($buffer)) {
            $buffer = $this->uniqueByDni($buffer);
            if ($this->updateExisting) {
                DB::table('natural_people')->upsert(
                    $buffer,
                    ['dni'],
                    ['nombres', 'apellido_paterno', 'apellido_materno', 'updated_at']
                );
            } else {
                $dniList = array_column($buffer, 'dni');
                $existing = DB::table('natural_people')
                    ->whereIn('dni', $dniList)
                    ->pluck('dni')
                    ->all();
                $existingLookup = array_flip($existing);
                $newRows = array_values(array_filter(
                    $buffer,
                    fn($row) => !isset($existingLookup[$row['dni']])
                ));
                if (!empty($newRows)) {
                    DB::table('natural_people')->insert($newRows);
                }
            }
        }
    }

    public function startRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    public function batchSize(): int
    {
        return 2000;
    }

    private function detectHeaderMap(array $row): array
    {
        $map = [];

        foreach ($row as $index => $value) {
            $header = $this->normalizeHeader($value);
            if ($header === '') {
                continue;
            }
            $header = str_replace(['_', '-', '.'], ' ', $header);
            $header = preg_replace('/\s+/', ' ', $header);

            if (in_array($header, ['DNI'], true)) {
                $map['dni'] = $index;
                continue;
            }

            if (in_array($header, ['NOMBRE', 'NOMBRES'], true)) {
                $map['nombres'] = $index;
                continue;
            }

            if (in_array($header, ['APELLIDO PATERNO', 'PRIMER APELLIDO', 'APELLIDO 1', 'APELLIDO1'], true)) {
                $map['apellido_paterno'] = $index;
                continue;
            }

            if (in_array($header, ['APELLIDO MATERNO', 'SEGUNDO APELLIDO', 'APELLIDO 2', 'APELLIDO2'], true)) {
                $map['apellido_materno'] = $index;
                continue;
            }

            if (in_array($header, ['APELLIDO', 'APELLIDOS'], true)) {
                $map['apellidos'] = $index;
            }
        }

        return $map;
    }

    private function normalizeHeader($value): string
    {
        $text = strtoupper(trim((string) $value));
        if ($text === '') {
            return '';
        }

        return strtr($text, [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'Ñ' => 'N',
        ]);
    }

    private function valueAt(array $row, string $key): string
    {
        $index = $this->columnMap[$key] ?? null;
        if ($index === null) {
            return '';
        }

        return trim((string) ($row[$index] ?? ''));
    }

    private function uniqueByDni(array $rows): array
    {
        $unique = [];
        foreach ($rows as $row) {
            $dni = $row['dni'] ?? null;
            if (!$dni || isset($unique[$dni])) {
                continue;
            }
            $unique[$dni] = $row;
        }

        return array_values($unique);
    }
}
