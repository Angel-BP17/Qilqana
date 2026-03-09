<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
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
                content: "PÃ¡gina " counter(page) " de " counter(pages);
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
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-logo {
            width: 30%;
            text-align: left;
            vertical-align: middle;
        }

        .header-logo img {
            width: 220px;
        }

        .header-title {
            width: 40%;
            text-align: center;
            vertical-align: middle;
        }

        .header-spacer {
            width: 30%;
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
            margin-bottom: 10px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .header-table,
        .header-table td {
            border: none;
        }

        .table-head th {
            background: #abcaed;
            color: rgb(0, 0, 0);
            text-transform: uppercase;
            font-weight: bold;
            padding: 10px;
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
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="{{ public_path('img/logo-ugel.png') }}" alt="Logo UGEL">
                </td>
                <td class="header-title">
                    <div class="title">{{ $title }}</div>
                    <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
                </td>
                <td class="header-spacer"></td>
            </tr>
        </table>
    </div>

    @if (($filters['search'] ?? null) || ($filters['signature_status'] ?? null))
        <div class="filters">
            <strong>Filtros aplicados:</strong>
            @if ($filters['search'] ?? null)
                Busqueda: "{{ $filters['search'] }}"
            @endif
            @if ($filters['signature_status'] ?? null)
                | Estado: {{ ucfirst($filters['signature_status']) }}
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th colspan="{{ $type === 'sent' || $type === 'resolution' ? 9 : 9 }}" class="title-header">
                    {{ $title }}
                </th>
            </tr>
            @if ($type === 'sent')
                <tr class="table-head">
                    <th>ID</th>
                    <th>No. cargo</th>
                    <th>Periodo</th>
                    <th>Fecha</th>
                    <th>Interesado</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Fecha firma</th>
                    <th>Firma</th>
                </tr>
            @elseif ($type === 'resolution')
                <tr class="table-head">
                    <th>ID</th>
                    <th>No. cargo</th>
                    <th>Periodo</th>
                    <th>RD</th>
                    <th>Fecha</th>
                    <th>Nombres y apellidos</th>
                    <th>DNI</th>
                    <th>Asunto</th>
                    <th>Fecha firma</th>
                    <th>Firma</th>
                </tr>
            @else
                <tr class="table-head">
                    <th>ID</th>
                    <th>NÂ° Cargo</th>
                    <th>Periodo</th>
                    <th>Enviado por</th>
                    <th>Interesado</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Fecha firma</th>
                    <th>Firma</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($charges as $charge)
                @php
                    $interesadoLabel = '';
                    $signatureImage = null;
                    if ($charge->tipo_interesado === 'Persona Juridica') {
                        $interesadoLabel = $charge->legalEntity?->razon_social ?: $charge->legalEntity?->ruc ?: 'N/A';
                    } else {
                        $interesadoLabel = trim(
                            ($charge->naturalPerson?->nombres ?? '') . ' ' . ($charge->naturalPerson?->apellidos ?? ''),
                        );
                        if ($interesadoLabel === '') {
                            $interesadoLabel = $charge->naturalPerson?->dni ?? 'N/A';
                        }
                    }

                    if (
                        $charge->signature?->signature_status === 'firmado' &&
                        $charge->signature?->signature_root &&
                        \Illuminate\Support\Facades\Storage::disk('local')->exists($charge->signature->signature_root)
                    ) {
                        $signatureSvg = \Illuminate\Support\Facades\Storage::disk('local')->get(
                            $charge->signature->signature_root,
                        );
                        $signatureImage = 'data:image/svg+xml;base64,' . base64_encode($signatureSvg);
                    }
                    $signatureCompletedAt = $charge->signature?->signature_completed_at;
                    $signatureCompletedLabel = $signatureCompletedAt
                        ? $signatureCompletedAt->format('d/m/Y h:i:s A')
                        : 'â€”';
                @endphp
                @if ($type === 'sent')
                    <tr>
                        <td>{{ $charge->id }}</td>
                        <td>{{ $charge->n_charge }}</td>
                        <td>{{ $charge->charge_period ?? 'N/A' }}</td>
                        <td>{{ optional($charge->created_at)->format('Y-m-d') }}</td>
                        <td>{{ $interesadoLabel }}</td>
                        <td>{{ Str::limit($charge->asunto ?? '', 60) }}</td>
                        <td>{{ ucfirst($charge->signature?->signature_status ?? 'pendiente') }}</td>
                        <td>{{ $signatureCompletedLabel }}</td>
                        <td>
                            @if ($signatureImage)
                                <img src="{{ $signatureImage }}" alt="Firma" style="height: 28px;">
                            @else
                                X
                            @endif
                        </td>
                    </tr>
                @elseif ($type === 'resolution')
                    <tr>
                        <td>{{ $charge->id }}</td>
                        <td>{{ $charge->n_charge }}</td>
                        <td>{{ $charge->charge_period ?? 'N/A' }}</td>
                        <td>{{ $charge->resolucion?->rd ?? 'N/A' }}</td>
                        <td>
                            {{ $charge->resolucion?->fecha ? \Carbon\Carbon::parse($charge->resolucion->fecha)->format('Y-m-d') : '' }}
                        </td>
                        <td>{{ $charge->resolucion?->nombres_apellidos ?? 'N/A' }}</td>
                        <td>{{ $charge->resolucion?->dni ?? 'N/A' }}</td>
                        <td>{{ Str::limit($charge->resolucion?->asunto ?? '', 60) }}</td>
                        <td>{{ $signatureCompletedLabel }}</td>
                        <td>
                            @if ($signatureImage)
                                <img src="{{ $signatureImage }}" alt="Firma" style="height: 28px;">
                            @else
                                X
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $charge->id }}</td>
                        <td>{{ $charge->n_charge }}</td>
                        <td>{{ $charge->charge_period ?? 'N/A' }}</td>
                        <td>{{ trim(($charge->user?->name ?? '') . ' ' . ($charge->user?->last_name ?? '')) ?: 'N/A' }}
                        </td>
                        <td>{{ $interesadoLabel }}</td>
                        <td>{{ Str::limit($charge->asunto ?? '', 60) }}</td>
                        <td>{{ ucfirst($charge->signature?->signature_status ?? 'pendiente') }}</td>
                        <td>{{ $signatureCompletedLabel }}</td>
                        <td>
                            @if ($signatureImage)
                                <img src="{{ $signatureImage }}" alt="Firma" style="height: 28px;">
                            @else
                                X
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

