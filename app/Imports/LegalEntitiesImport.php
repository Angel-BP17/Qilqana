<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LegalEntitiesImport implements ToCollection, WithBatchInserts, WithCalculatedFormulas, WithChunkReading, WithStartRow
{
    public function collection(Collection $rows): void
    {
        $buffer = [];
        $now = now();

        foreach ($rows as $row) {
            $rowData = $row->toArray();
            if (empty(array_filter($rowData))) {
                continue;
            }

            $ruc = trim((string) ($rowData[0] ?? ''));
            $razonSocial = trim((string) ($rowData[1] ?? ''));
            $district = trim((string) ($rowData[2] ?? ''));

            $ruc = ltrim($ruc, "'");
            if ($ruc === '') {
                continue;
            }

            $buffer[] = [
                'ruc' => $ruc,
                'razon_social' => $razonSocial !== '' ? $razonSocial : null,
                'district' => $district !== '' ? $district : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($buffer)) {
            DB::table('legal_entities')->upsert(
                $buffer,
                ['ruc'],
                ['razon_social', 'district', 'updated_at']
            );
        }
    }

    public function startRow(): int
    {
        return 2;
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
