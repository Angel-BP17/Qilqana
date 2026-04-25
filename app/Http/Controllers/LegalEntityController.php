<?php

namespace App\Http\Controllers;

use App\Filters\LegalEntityFilter;
use App\Http\Requests\LegalEntity\ImportLegalEntityRequest;
use App\Imports\LegalEntitiesImport;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Representative;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LegalEntityController extends Controller
{
    public function index(Request $request, LegalEntityFilter $filter)
    {
        $this->authorize('viewAny', LegalEntity::class);

        $query = LegalEntity::with(['representative.naturalPerson']);
        $filter->apply($query, $request->only('search'));

        $legalEntities = $query->orderBy('razon_social')->paginate(10)->withQueryString();
        $representatives = Representative::with('naturalPerson')->get()->sortBy(function ($rep) {
            return $rep->naturalPerson?->nombres;
        });

        return view('legal-entities.index', compact('legalEntities', 'representatives'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', LegalEntity::class);

        $data = $this->validateData($request);

        if (! empty($data['representative_dni'])) {
            $person = NaturalPerson::firstOrCreate(
                ['dni' => $data['representative_dni']],
                ['nombres' => $data['representative_name']]
            );

            $representative = Representative::create([
                'natural_person_id' => $person->id,
                'cargo' => $data['representative_cargo'],
                'fecha_desde' => $data['representative_since'],
            ]);

            $data['representative_id'] = $representative->id;
        }

        LegalEntity::create($data);

        return redirect()->route('legal-entities.index')->with('success', 'Persona jurídica creada correctamente');
    }

    public function update(Request $request, LegalEntity $legalEntity)
    {
        $this->authorize('update', $legalEntity);

        $data = $this->validateData($request, $legalEntity->id);

        if (! empty($data['representative_dni'])) {
            $person = NaturalPerson::updateOrCreate(
                ['dni' => $data['representative_dni']],
                ['nombres' => $data['representative_name']]
            );

            $representative = $legalEntity->representative;
            if ($representative) {
                $representative->update([
                    'natural_person_id' => $person->id,
                    'cargo' => $data['representative_cargo'],
                    'fecha_desde' => $data['representative_since'],
                ]);
            } else {
                $representative = Representative::create([
                    'natural_person_id' => $person->id,
                    'cargo' => $data['representative_cargo'],
                    'fecha_desde' => $data['representative_since'],
                ]);
                $data['representative_id'] = $representative->id;
            }
        }

        $legalEntity->update($data);

        return redirect()->route('legal-entities.index')->with('success', 'Persona jurídica actualizada correctamente');
    }

    public function destroy(LegalEntity $legalEntity)
    {
        $this->authorize('delete', $legalEntity);

        request()->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $legalEntity->delete();

        return redirect()->route('legal-entities.index')->with('success', 'Persona jurídica eliminada correctamente');
    }

    public function import(ImportLegalEntityRequest $request)
    {
        $this->authorize('create', LegalEntity::class);

        Excel::import(new LegalEntitiesImport, $request->file('archivo_excel'));

        return redirect()->route('legal-entities.index')->with('success', 'Personas jurídicas importadas correctamente');
    }

    public function downloadTemplate()
    {
        $this->authorize('viewAny', LegalEntity::class);

        $filePath = storage_path('app/public/templates/Plantilla_Personas_Juridicas.xlsx');

        if (! file_exists($filePath)) {
            if (! is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            return redirect()->back()->withErrors(['La plantilla de personas jurídicas no se encuentra disponible.']);
        }

        return response()->download($filePath, 'Plantilla_Importacion_Personas_Juridicas.xlsx');
    }

    protected function validateData(Request $request, $id = null): array
    {
        return $request->validate([
            'ruc' => ['nullable', 'string', 'max:255', 'unique:legal_entities,ruc'.($id ? ",$id" : '')],
            'razon_social' => ['nullable', 'string', 'max:255', 'unique:legal_entities,razon_social'.($id ? ",$id" : '')],
            'district' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'representative_dni' => ['nullable', 'string', 'max:10'],
            'representative_name' => ['nullable', 'string', 'max:255'],
            'representative_cargo' => ['nullable', 'string', 'max:255'],
            'representative_since' => ['nullable', 'date'],
        ]);
    }
}
