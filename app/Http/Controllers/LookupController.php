<?php

namespace App\Http\Controllers;

use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class LookupController extends Controller
{
    public function naturalPersonByDni(string $dni): JsonResponse
    {
        $dni = trim($dni);
        $person = NaturalPerson::where('dni', $dni)->first();

        if (! $person) {
            $apiKey = (string) config('services.apisperu.key', '');
            if ($apiKey !== '') {
                try {
                    $response = Http::withToken($apiKey)
                        ->acceptJson()
                        ->get("https://dniruc.apisperu.com/api/v1/dni/{$dni}");

                    if ($response->ok()) {
                        $data = $response->json();
                        $dniValue = $data['dni'] ?? null;

                        if (! empty($dniValue)) {
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
                    }
                } catch (\Throwable $e) {
                    return response()->json(['message' => 'Error consultando apisperu.com'], 502);
                }
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

        $apiKey = (string) config('services.apisperu.key', '');
        if ($apiKey === '') {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        try {
            $rucResponse = Http::withToken($apiKey)
                ->acceptJson()
                ->get("https://dniruc.apisperu.com/api/v1/ruc/{$ruc}");

            if (! $rucResponse->ok()) {
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
            return response()->json(['message' => 'Error consultando apisperu.com'], 502);
        }
    }
}
