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
        }

        .header {
            text-align: center;
            margin-bottom: 6px;
            position: relative;
            min-height: 64px;
        }

        .header img {
            width: 250px;
            position: absolute;
            left: 10px;
            top: 0;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        p {
            text-align: center;
            font-size: 12px;
            margin: 5px 0;
        }

        .filters {
            margin-bottom: 15px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            font-size: 11px;
            word-wrap: break-word;
            white-space: normal;
        }

        th {
            background: #abcaed;
            color: rgb(0, 0, 0);
            text-transform: uppercase;
            font-weight: bold;
            padding: 10px;
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
    <div class="header">
        <img src="{{ public_path('img/logo-ugel.png') }}" alt="Logo UGEL">
        <div class="title">REPORTE DE RESOLUCIONES</div>
        <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="title-header">Detalle de resoluciones</div>

    @if ($filtros['search'] || $filtros['periodo'])
        <div class="filters">
            <strong>Filtros aplicados:</strong>
            @if ($filtros['search'])
                Búsqueda: "{{ $filtros['search'] }}"
            @endif
            @if ($filtros['periodo'])
                | Periodo: {{ $filtros['periodo'] }}
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>RD</th>
                <th>Fecha</th>
                <th>Nombres y Apellidos</th>
                <th>DNI</th>
                <th>Asunto</th>
                <th>Periodo</th>
                <th>Procedencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resoluciones as $resolucion)
                <tr>
                    <td>{{ $resolucion->id }}</td>
                    <td>{{ $resolucion->rd }}</td>
                    <td>{{ $resolucion->fecha ? \Carbon\Carbon::parse($resolucion->fecha)->format('d/m/Y') : '' }}</td>
                    <td>{{ $resolucion->nombres_apellidos }}</td>
                    <td>{{ $resolucion->dni ?? 'N/A' }}</td>
                    <td>{{ Str::limit($resolucion->asunto, 50) }}</td>
                    <td>{{ $resolucion->periodo }}</td>
                    <td>{{ $resolucion->procedencia }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total de registros: {{ $resoluciones->count() }}
    </div>
</body>

</html>
