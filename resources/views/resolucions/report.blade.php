<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Resoluciones</title>
    <style>
        @page {
            margin: 40px 25px;
            size: A4;
        }

        @page :right {
            @top-right {
                font-size: 12px;
                font-weight: bold;
            }

            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 10px;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .title-header {
            background: #0D47A1;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 12px;
            text-align: center;
            margin-top: 8px;
            margin-bottom: 0px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-top: 1px solid #333;
            border-left: 1px solid #333;
            margin-top: 0px;
        }

        th,
        td {
            border-right: 1px solid #333;
            border-bottom: 1px solid #333;
            padding: 6px;
            text-align: center;
            font-size: 11px;
            word-wrap: break-word;
            white-space: normal;
        }

        th {
            background-color: #abcaed;
            color: rgb(0, 0, 0);
            text-transform: uppercase;
            font-weight: bold;
            padding: 10px 6px;
        }

        .json-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .json-table th,
        .json-table td {
            border: 1px solid #aaa;
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
        }

        .json-container {
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 5px; margin-top: -10px;">
        <tr style="border: none;">
            <td style="width: 30%; border: none; padding: 0; text-align: left; vertical-align: middle;">
                <img src="{{ public_path('img/logo-ugel.png') }}" style="width: 160px; height: auto; display: block;">
            </td>
            <td style="width: 70%; border: none; padding: 0; text-align: center; vertical-align: middle;">
                <div style="font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">REPORTE DE RESOLUCIONES</div>
                <div style="font-size: 11px; color: #555;">Fecha de generación: {{ now()->format('d/m/Y h:i A') }}</div>
                @if (($filtros['search'] ?? null) || ($filtros['search_rd'] ?? null) || ($filtros['search_asunto'] ?? null) || $filtros['periodo'])
                    <div style="font-size: 11px; text-align: center; margin-top: 5px;">
                        <strong>Filtros aplicados:</strong>
                        @if ($filtros['search'] ?? null)
                            Búsqueda: "{{ $filtros['search'] }}"
                        @endif
                        @if ($filtros['search_rd'] ?? null)
                            RD: "{{ $filtros['search_rd'] }}"
                        @endif
                        @if ($filtros['search_asunto'] ?? null)
                            Asunto: "{{ $filtros['search_asunto'] }}"
                        @endif
                        @if ($filtros['periodo'])
                            | Periodo: {{ $filtros['periodo'] }}
                        @endif
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <div class="title-header">Detalle de resoluciones</div>

    <table>
        <tr>
            <th>ID</th>
            <th>RD</th>
            <th>FECHA</th>
            <th>NOMBREYAPELLIDO</th>
            <th>DNI O RUC</th>
            <th>TIPO DE ASUNTO</th>
            <th>NIVEL</th>
            <th>PERIODO</th>
            <th>FIRMA</th>
        </tr>
        @foreach ($resoluciones as $resolucion)
            <tr>
                <td>{{ $resolucion->id }}</td>
                <td>{{ $resolucion->type?->abreviacion ?? 'RD' }} {{ $resolucion->rd }}</td>
                <td>{{ $resolucion->fecha ? \Carbon\Carbon::parse($resolucion->fecha)->format('d/m/Y') : '' }}</td>
                <td>{{ $resolucion->nombres_apellidos }}</td>
                <td>{{ implode(', ', array_filter([$resolucion->dni, $resolucion->ruc])) ?: 'N/A' }}</td>
                <td>{{ $resolucion->asuntoType?->name ?? '---' }}</td>
                <td>{{ $resolucion->levelModality?->name ?? '---' }}</td>
                <td>{{ $resolucion->periodo }}</td>
                <td>
                    @php
                        $sigRoot = $resolucion->charge?->signature?->signature_root;
                        $sigPath = $sigRoot ? storage_path('app/' . $sigRoot) : null;
                    @endphp
                    @if ($resolucion->signature_status === 'firmado' && $sigPath && file_exists($sigPath))
                        <img src="{{ $sigPath }}" style="width: 80px; height: 25px; display: block; margin: 0 auto;">
                    @else
                        {{ $resolucion->signature_status === 'firmado' ? 'FIRMADO' : ($resolucion->signature_status === 'rechazado' ? 'RECHAZADO' : 'PENDIENTE') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <div class="footer">
        Total de registros: {{ $resoluciones->count() }}
    </div>
</body>

</html>
