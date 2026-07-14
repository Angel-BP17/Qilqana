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
            color: #333;
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
            table-layout: fixed;
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

        .text-left {
            text-align: left !important;
        }

        .text-capitalize {
            text-transform: capitalize !important;
        }

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
    <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 5px; margin-top: -10px;">
        <tr style="border: none;">
            <td style="width: 30%; border: none; padding: 0; text-align: left; vertical-align: middle;">
                <img src="{{ public_path('img/logo-ugel.png') }}" style="width: 160px; height: auto; display: block;">
            </td>
            <td style="width: 70%; border: none; padding: 0; text-align: center; vertical-align: middle;">
                <div style="font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">{{ $title }}</div>
                <div style="font-size: 11px; color: #555;">Fecha de generación: {{ now()->format('d/m/Y h:i A') }}</div>
                @if (($filters['search'] ?? null) || ($filters['signature_status'] ?? null) || ($filters['period'] ?? null))
                    <div style="font-size: 11px; text-align: center; margin-top: 5px;">
                        <strong>Filtros aplicados:</strong>
                        @if ($filters['period'] ?? null)
                            Periodo: {{ $filters['period'] }}
                        @endif
                        @if ($filters['signature_status'] ?? null)
                            | Estado: {{ ucfirst($filters['signature_status']) }}
                        @endif
                        @if ($filters['search'] ?? null)
                            | Búsqueda: "{{ $filters['search'] }}"
                        @endif
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <div class="title-header">{{ $title }}</div>

    <table>
        <thead>
            @if ($type === 'sent')
                <tr>
                    <th style="width: 4%;">ID</th>
                    <th style="width: 10%;">No. Cargo</th>
                    <th style="width: 8%;">Periodo</th>
                    <th style="width: 9%;">Fecha</th>
                    <th style="width: 22%;">Interesado</th>
                    <th style="width: 22%;">Asunto</th>
                    <th style="width: 8%;">Estado</th>
                    <th style="width: 9%;">Fecha Firma</th>
                    <th style="width: 8%;">Firma</th>
                </tr>
            @elseif ($type === 'resolution')
                <tr>
                    <th style="width: 4%;">ID</th>
                    <th style="width: 8%;">Cargo</th>
                    <th style="width: 6%;">Año</th>
                    <th style="width: 9%;">RD</th>
                    <th style="width: 9%;">Fecha RD</th>
                    <th style="width: 20%;">Interesado</th>
                    <th style="width: 9%;">DNI</th>
                    <th style="width: 18%;">Asunto</th>
                    <th style="width: 9%;">Fecha Firma</th>
                    <th style="width: 8%;">Firma</th>
                </tr>
            @else
                <tr>
                    <th style="width: 4%;">ID</th>
                    <th style="width: 10%;">No. Cargo</th>
                    <th style="width: 8%;">Periodo</th>
                    <th style="width: 15%;">Enviado por</th>
                    <th style="width: 18%;">Interesado</th>
                    <th style="width: 18%;">Asunto</th>
                    <th style="width: 9%;">Estado</th>
                    <th style="width: 10%;">Fecha Firma</th>
                    <th style="width: 8%;">Firma</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($charges as $charge)
                @php
                    $signatureCompletedLabel = $charge->signature?->signature_completed_at 
                        ? $charge->signature->signature_completed_at->format('d/m/Y H:i') 
                        : '---';
                    $sigRoot = $charge->signature?->signature_root;
                    $sigPath = $sigRoot ? storage_path('app/' . $sigRoot) : null;
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
                            @if ($charge->signature?->signature_status === 'firmado' && $sigPath && file_exists($sigPath))
                                <img src="{{ $sigPath }}" style="width: 80px; height: 20px; display: block; margin: 0 auto;">
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
                            @if ($charge->signature?->signature_status === 'firmado' && $sigPath && file_exists($sigPath))
                                <img src="{{ $sigPath }}" style="width: 80px; height: 20px; display: block; margin: 0 auto;">
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
                            @if ($charge->signature?->signature_status === 'firmado' && $sigPath && file_exists($sigPath))
                                <img src="{{ $sigPath }}" style="width: 80px; height: 20px; display: block; margin: 0 auto;">
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
