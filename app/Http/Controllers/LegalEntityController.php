<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegalEntity\ImportLegalEntityRequest;
use App\Imports\LegalEntitiesImport;
use App\Models\LegalEntity;
use App\Models\Representative;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LegalEntityController extends Controller
{
    public function index()
    {
        $this->ensureAllowed(request()->user(), ['modulo personas juridicas', 'legal-entities.view']);

        $legalEntities = LegalEntity::with('representative')->orderBy('razon_social')->paginate(10);
        $representatives = Representative::orderBy('nombre')->get();

        return view('legal-entities.index', compact('legalEntities', 'representatives'));
    }

    public function store(Request $request)
    {
        $this->ensureAllowed($request->user(), ['legal-entities.create']);

        $data = $request->validate([
            'ruc' => ['nullable', 'string', 'max:255', 'unique:legal_entities,ruc'],
            'razon_social' => ['nullable', 'string', 'max:255', 'unique:legal_entities,razon_social'],
            'district' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'representative_id' => ['nullable', 'integer', 'exists:representatives,id'],
        ]);

        LegalEntity::create($data);

        return redirect()->route('legal-entities.index')->with('success', 'Persona juridica creada correctamente');
    }

    public function update(Request $request, LegalEntity $legalEntity)
    {
        $this->ensureAllowed($request->user(), ['legal-entities.edit']);

        $data = $request->validate([
            'ruc' => ['nullable', 'string', 'max:255', 'unique:legal_entities,ruc,' . $legalEntity->id],
            'razon_social' => ['nullable', 'string', 'max:255', 'unique:legal_entities,razon_social,' . $legalEntity->id],
            'district' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'representative_id' => ['nullable', 'integer', 'exists:representatives,id'],
        ]);

        $legalEntity->update($data);

        return redirect()->route('legal-entities.index')->with('success', 'Persona juridica actualizada correctamente');
    }

    public function destroy(LegalEntity $legalEntity)
    {
        $this->ensureAllowed(request()->user(), ['legal-entities.delete']);

        request()->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $legalEntity->delete();

        return redirect()->route('legal-entities.index')->with('success', 'Persona juridica eliminada correctamente');
    }

    public function import(ImportLegalEntityRequest $request)
    {
        $this->ensureAllowed($request->user(), ['legal-entities.create']);

        Excel::import(new LegalEntitiesImport(), $request->file('archivo_excel'));

        return redirect()->route('legal-entities.index')->with('success', 'Personas juridicas importadas correctamente');
    }

    protected function ensureAllowed(?object $user, array $permissions): void
    {
        $allowed = $user?->hasRole('ADMINISTRADOR') ?? false;
        if (!$allowed) {
            foreach ($permissions as $permission) {
                if ($user?->can($permission)) {
                    $allowed = true;
                    break;
                }
            }
        }

        abort_unless($allowed, 403);
    }
}
