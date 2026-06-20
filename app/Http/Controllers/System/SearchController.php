<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\AsuntoType;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Resolucion;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function naturalPeople(Request $request)
    {
        $search = $request->input('q');
        $results = NaturalPerson::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('dni', 'like', "%{$search}%")
                        ->orWhere('cedula', 'like', "%{$search}%")
                        ->orWhere('nombres', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%")
                        ->orWhere('apellido_materno', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get(['id', 'dni', 'cedula', 'nombres', 'apellido_paterno', 'apellido_materno']);

        return response()->json([
            'results' => $results->map(fn ($p) => [
                'id' => $p->id,
                'text' => ($p->dni ? "{$p->dni}" : "CED-{$p->cedula}")." - {$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}",
                'dni' => $p->dni,
                'cedula' => $p->cedula,
            ]),
        ]);
    }

    public function byCedula(string $cedula)
    {
        $person = NaturalPerson::where('cedula', $cedula)->first();

        if (! $person) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $person->id,
                'cedula' => $person->cedula,
                'nombres' => $person->nombres,
                'apellido_paterno' => $person->apellido_paterno,
                'apellido_materno' => $person->apellido_materno,
            ],
        ]);
    }

    public function legalEntities(Request $request)
    {
        $search = $request->input('q');
        $results = LegalEntity::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ruc', 'like', "%{$search}%")
                        ->orWhere('razon_social', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get(['id', 'ruc', 'razon_social']);

        return response()->json([
            'results' => $results->map(fn ($e) => [
                'id' => $e->id,
                'text' => "{$e->ruc} - {$e->razon_social}",
                'ruc' => $e->ruc,
            ]),
        ]);
    }

    public function users(Request $request)
    {
        $search = $request->input('q');
        $results = User::query()
            ->where('id', '!=', auth()->id())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('dni', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get(['id', 'dni', 'name', 'last_name']);

        return response()->json([
            'results' => $results->map(fn ($u) => [
                'id' => $u->id,
                'text' => "{$u->dni} - {$u->name} {$u->last_name}",
                'dni' => $u->dni,
            ]),
        ]);
    }

    public function pendingResolutions(Request $request)
    {
        $search = $request->input('q');

        $results = Resolucion::query()
            ->with('type')
            ->whereDoesntHave('charges') // Solo las que no tienen cargo
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('rd', 'like', "%{$search}%")
                        ->orWhere('nombres_apellidos', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%")
                        ->orWhere('ruc', 'like', "%{$search}%")
                        ->orWhere('asunto', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('fecha')
            ->limit(30)
            ->get(['id', 'rd', 'nombres_apellidos', 'fecha', 'resolucion_type_id']);

        return response()->json([
            'results' => $results->map(fn ($r) => [
                'id' => $r->id,
                'text' => '['.($r->type?->name ?? 'S/T').'] '.($r->type?->abreviacion ?? 'RD')." {$r->rd} - {$r->nombres_apellidos} ({$r->fecha->format('d/m/Y')})",
            ]),
        ]);
    }

    public function asuntosByResolutionType(int $id)
    {
        $asuntos = AsuntoType::whereHas('resolucionTypes', function ($q) use ($id) {
            $q->where('resolucion_types.id', $id);
        })->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'results' => $asuntos->map(fn ($a) => [
                'id' => $a->id,
                'text' => $a->name,
            ]),
        ]);
    }
}
