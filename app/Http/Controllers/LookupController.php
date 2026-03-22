<?php

namespace App\Http\Controllers;

use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LookupController extends Controller
{
    public function naturalPersonByDni(string $dni): JsonResponse
    {
        $dni = trim($dni);
        $person = NaturalPerson::where('dni', $dni)->first();

        if (!$person) {
            $apiKey = (string) env('API_DEV_PERU_KEY', '');
            if ($apiKey !== '') {
                try {
                    $response = Http::withToken($apiKey)
                        ->acceptJson()
                        ->get("https://apiperu.dev/api/dni/{$dni}");

                    if ($response->ok()) {
                        $payload = $response->json();
                        $data = $payload['data'] ?? [];
                        $dniValue = $data['dni'] ?? $data['numero'] ?? null;
                        if (!empty($dniValue)) {
                            return response()->json([
                                'data' => [
                                    'id' => null,
                                    'dni' => $dniValue ?? $dni,
                                    'nombres' => $data['nombres'] ?? null,
                                    'apellido_paterno' => $data['apellido_paterno'] ?? null,
                                    'apellido_materno' => $data['apellido_materno'] ?? null,
                                    'apellidos' => trim(
                                        ($data['apellido_paterno'] ?? '') . ' ' . ($data['apellido_materno'] ?? '')
                                    ) ?: null,
                                ],
                            ]);
                        }
                        Log::warning('LookupController.naturalPersonByDni:apiperu_empty', [
                            'dni' => $dni,
                            'response' => $payload,
                        ]);
                    } else {
                        Log::warning('LookupController.naturalPersonByDni:apiperu_non_ok', [
                            'dni' => $dni,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::error('LookupController.naturalPersonByDni:apiperu_exception', [
                        'dni' => $dni,
                        'message' => $e->getMessage(),
                        'exception' => $e,
                    ]);
                    return response()->json(['message' => 'Error consultando apiperu.dev'], 502);
                }
            } else {
                Log::warning('LookupController.naturalPersonByDni:missing_api_key', [
                    'dni' => $dni,
                ]);
            }

            return response()->json(['message' => 'No encontrado'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $person->id,
                'dni' => $person->dni,
                'nombres' => $person->nombres,
                'apellido_paterno' => $person->apellido_paterno,
                'apellido_materno' => $person->apellido_materno,
                'apellidos' => $person->apellidos,
            ],
        ]);
    }

    public function legalEntityByRuc(string $ruc): JsonResponse
    {
        $ruc = trim($ruc);
        $entity = LegalEntity::with('representative')
            ->where('ruc', $ruc)
            ->first();

        if ($entity) {
            $rep = $entity->representative;
            $person = $rep?->naturalPerson;
            
            return response()->json([
                'data' => [
                    'id' => $entity->id,
                    'ruc' => $entity->ruc,
                    'razon_social' => $entity->razon_social,
                    'district' => $entity->district,
                    'representative' => $rep ? [
                        'id' => $rep->id,
                        'dni' => $person?->dni ?? null,
                        'nombres' => $person?->nombres ?? null,
                        'apellido_paterno' => $person?->apellido_paterno ?? null,
                        'apellido_materno' => $person?->apellido_materno ?? null,
                        'cargo' => $rep->cargo,
                        'fecha_desde' => $rep->fecha_desde,
                    ] : null,
                ],
            ]);
        }

        $apiKey = (string) env('API_DEV_PERU_KEY', '');
        if ($apiKey === '') {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        try {
            $rucResponse = Http::withToken($apiKey)
                ->acceptJson()
                ->get("https://apiperu.dev/api/ruc/{$ruc}");

            if (!$rucResponse->ok()) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $rucPayload = $rucResponse->json();
            $rucData = $rucPayload['data'] ?? [];
            if (empty($rucData['ruc'])) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $repResponse = Http::withToken($apiKey)
                ->acceptJson()
                ->post('https://apiperu.dev/api/ruc-representantes', [
                    'ruc' => $ruc,
                ]);

            $representatives = null;
            if ($repResponse->ok()) {
                $repPayload = $repResponse->json();
                $repData = $repPayload['data'] ?? [];
                if (!empty($repData) && is_array($repData)) {
                    $first = $repData[0] ?? null;
                    if (is_array($first)) {
                        $representatives = [
                            'dni' => $first['numero_de_documento'] ?? null,
                            'nombre' => $first['nombre'] ?? null,
                            'cargo' => $first['cargo'] ?? null,
                            'fecha_desde' => $first['fecha_desde'] ?? null,
                        ];
                    }
                }
            }

            return response()->json([
                'data' => [
                    'id' => null,
                    'ruc' => $rucData['ruc'] ?? $ruc,
                    'razon_social' => $rucData['nombre_o_razon_social'] ?? null,
                    'district' => $rucData['distrito'] ?? null,
                    'representative' => $representatives,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error consultando apiperu.dev'], 502);
        }
    }
}
