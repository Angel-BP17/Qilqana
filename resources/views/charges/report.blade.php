<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 40px 25px;
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .title-header {
            background: #0D47A1;
            color: white;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
            text-align: center;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-logo img {
            width: 180px;
        }
        .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        .header-date {
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .filters {
            margin-bottom: 10px;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.main-table th {
            background: #E3F2FD;
            color: #0D47A1;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 9px;
            padding: 8px 4px;
            border: 1px solid #BBDEFB;
        }
        table.main-table td {
            border: 1px solid #eee;
            padding: 6px 4px;
            text-align: center;
            font-size: 10px;
            word-wrap: break-word;
        }
        .text-left { text-align: left !important; }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 10px;
            font-style: italic;
            color: #777;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="header-logo" style="width: 30%;">
                <img src="{{ public_path('img/logo-ugel.png') }}" alt="Logo UGEL">
            </td>
            <td class="header-title" style="width: 40%;">
                {{ $title }}
            </td>
            <td class="header-date" style="width: 30%;">
                Generado: {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    @if (($filters['search'] ?? null) || ($filters['signature_status'] ?? null) || ($filters['period'] ?? null))
        <div class="filters">
            <strong>Filtros:</strong>
            @if ($filters['search'] ?? null) Busqueda: "{{ $filters['search'] }}" @endif
            @if ($filters['signature_status'] ?? null) | Estado: {{ ucfirst($filters['signature_status']) }} @endif
            @if ($filters['period'] ?? null) | Periodo: {{ $filters['period'] }} @endif
        </div>
    @endif

    <table class="main-table">
        <thead>
            <tr>
                <th colspan="{{ $type === 'resolution' ? 10 : 9 }}" class="title-header">
                    {{ $title }}
                </th>
            </tr>
            @if ($type === 'sent')
                <tr>
                    <th style="width: 30px;">ID</th>
                    <th style="width: 60px;">No. Cargo</th>
                    <th style="width: 50px;">Periodo</th>
                    <th style="width: 70px;">Fecha</th>
                    <th>Interesado</th>
                    <th>Asunto</th>
                    <th style="width: 60px;">Estado</th>
                    <th style="width: 80px;">Fecha Firma</th>
                    <th style="width: 60px;">Firma</th>
                </tr>
            @elseif ($type === 'resolution')
                <tr>
                    <th style="width: 30px;">ID</th>
                    <th style="width: 50px;">Cargo</th>
                    <th style="width: 40px;">Año</th>
                    <th style="width: 70px;">RD</th>
                    <th style="width: 70px;">Fecha RD</th>
                    <th>Interesado</th>
                    <th style="width: 70px;">DNI</th>
                    <th>Asunto</th>
                    <th style="width: 80px;">Fecha Firma</th>
                    <th style="width: 60px;">Firma</th>
                </tr>
            @else
                <tr>
                    <th style="width: 30px;">ID</th>
                    <th style="width: 60px;">No. Cargo</th>
                    <th style="width: 50px;">Periodo</th>
                    <th>Enviado por</th>
                    <th>Interesado</th>
                    <th>Asunto</th>
                    <th style="width: 60px;">Estado</th>
                    <th style="width: 80px;">Fecha Firma</th>
                    <th style="width: 60px;">Firma</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($charges as $charge)
                @php
                    $signatureImage = null;
                    if ($charge->signature?->signature_status === 'firmado' && $charge->signature_content) {
                        $signatureImage = 'data:image/svg+xml;base64,' . base64_encode($charge->signature_content);
                    }
                    $signatureCompletedLabel = $charge->signature?->signature_completed_at 
                        ? $charge->signature->signature_completed_at->format('d/m/Y H:i') 
                        : '---';
                @endphp
                @if ($type === 'sent')
                    <tr>
                        <td>{{ $charge->id }}</td>
                        <td>{{ $charge->n_charge }}</td>
                        <td>{{ $charge->charge_period }}</td>
                        <td>{{ $charge->created_at->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $charge->interesado_label }}</td>
                        <td class="text-left">{{ Str::limit($charge->asunto, 80) }}</td>
                        <td>{{ ucfirst($charge->signature?->signature_status ?? 'pendiente') }}</td>
                        <td>{{ $signatureCompletedLabel }}</td>
                        <td>
                            @if ($signatureImage)
                                <img src="{{ $signatureImage }}" style="height: 25px;">
                            @else
                                ---
                            @endif
                        </td>
                    </tr>
                @elseif ($type === 'resolution')
                    <tr>
                        <td>{{ $charge->id }}</td>
                        <td>{{ $charge->n_charge }}</td>
                        <td>{{ $charge->charge_period }}</td>
                        <td>{{ $charge->resolucion?->rd ? ($charge->resolucion?->type?->abreviacion ?? 'RD') . ' ' . $charge->resolucion?->rd : 'N/A' }}</td>
                        <td>{{ $charge->resolucion?->formatted_fecha }}</td>
                        <td class="text-left">{{ $charge->resolucion?->nombres_apellidos }}</td>
                        <td>{{ $charge->resolucion?->dni }}</td>
                        <td class="text-left">{{ Str::limit($charge->asunto, 80) }}</td>
                        <td>{{ $signatureCompletedLabel }}</td>
                        <td>
                            @if ($signatureImage)
                                <img src="{{ $signatureImage }}" style="height: 25px;">
                            @else
                                ---
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $charge->id }}</td>
                        <td>{{ $charge->n_charge }}</td>
                        <td>{{ $charge->charge_period }}</td>
                        <td class="text-left text-capitalize">{{ strtolower($charge->user?->name . ' ' . $charge->user?->last_name) }}</td>
                        <td class="text-left">{{ $charge->interesado_label }}</td>
                        <td class="text-left">{{ Str::limit($charge->asunto, 80) }}</td>
                        <td>{{ ucfirst($charge->signature?->signature_status ?? 'pendiente') }}</td>
                        <td>{{ $signatureCompletedLabel }}</td>
                        <td>
                            @if ($signatureImage)
                                <img src="{{ $signatureImage }}" style="height: 25px;">
                            @else
                                ---
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total de registros: {{ $charges->count() }}
    </div>
</body>
</html>
