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
                        ->get("https://dniruc.apisperu.com/api/v1/dni/{$dni}");

                    if ($response->ok()) {
                        $data = $response->json();
                        $dniValue = $data['dni'] ?? null;

                        if (!empty($dniValue)) {
                            return response()->json([
                                'data' => [
                                    'id' => null,
                                    'dni' => $dniValue,
                                    'nombres' => $data['nombres'] ?? null,
                                    'apellido_paterno' => $data['apellidoPaterno'] ?? null,
                                    'apellido_materno' => $data['apellidoMaterno'] ?? null,
                                ],
                            ]);
                        }
                        Log::warning('LookupController.naturalPersonByDni:apisperu_empty', [
                            'dni' => $dni,
                            'response' => $data,
                        ]);
                    } else {
                        Log::warning('LookupController.naturalPersonByDni:apisperu_non_ok', [
                            'dni' => $dni,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::error('LookupController.naturalPersonByDni:apisperu_exception', [
                        'dni' => $dni,
                        'message' => $e->getMessage(),
                    ]);
                    return response()->json(['message' => 'Error consultando apisperu.com'], 502);
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
                ->get("https://dniruc.apisperu.com/api/v1/ruc/{$ruc}");

            if (!$rucResponse->ok()) {
                Log::warning('LookupController.legalEntityByRuc:apisperu_non_ok', [
                    'ruc' => $ruc,
                    'status' => $rucResponse->status(),
                ]);
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $rucData = $rucResponse->json();
            
            if (empty($rucData['ruc'])) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            return response()->json([
                'data' => [
                    'id' => null,
                    'ruc' => $rucData['ruc'],
                    'razon_social' => $rucData['razonSocial'] ?? null,
                    'district' => $rucData['distrito'] ?? null,
                    'representative' => null, // La nueva API no incluye representantes en esta llamada
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('LookupController.legalEntityByRuc:apisperu_exception', [
                'ruc' => $ruc,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error consultando apisperu.com'], 502);
        }
    }
}
