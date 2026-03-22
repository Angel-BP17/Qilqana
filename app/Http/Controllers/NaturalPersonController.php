<?php

namespace App\Http\Controllers;

use App\Filters\NaturalPersonFilter;
use App\Http\Requests\NaturalPerson\ImportNaturalPersonRequest;
use App\Imports\NaturalPeopleImport;
use App\Models\NaturalPerson;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NaturalPersonController extends Controller
{
    public function index(Request $request, NaturalPersonFilter $filter)
    {
        $this->authorize('viewAny', NaturalPerson::class);

        $query = NaturalPerson::query();
        $filter->apply($query, $request->only('search'));

        $naturalPeople = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('natural-people.index', compact('naturalPeople'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', NaturalPerson::class);

        $data = $this->validateData($request);
        NaturalPerson::create($data);

        return redirect()->route('natural-people.index')->with('success', 'Persona natural creada correctamente');
    }

    public function update(Request $request, NaturalPerson $naturalPerson)
    {
        $this->authorize('update', $naturalPerson);

        $data = $this->validateData($request);
        $naturalPerson->update($data);

        return redirect()->route('natural-people.index')->with('success', 'Persona natural actualizada correctamente');
    }

    public function destroy(Request $request, NaturalPerson $naturalPerson)
    {
        $this->authorize('delete', $naturalPerson);

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        if ($naturalPerson->charges()->exists()) {
            return redirect()
                ->route('natural-people.index')
                ->withErrors(['No se puede eliminar una persona natural con cargos asociados.']);
        }

        $naturalPerson->delete();

        return redirect()->route('natural-people.index')->with('success', 'Persona natural eliminada correctamente');
    }

    public function import(ImportNaturalPersonRequest $request)
    {
        $this->authorize('create', NaturalPerson::class);

        $updateExisting = $request->boolean('update_existing', true);
        Excel::import(new NaturalPeopleImport($updateExisting), $request->file('archivo_excel'));

        return redirect()->route('natural-people.index')->with('success', 'Personas naturales importadas correctamente');
    }

    public function downloadTemplate()
    {
        $this->authorize('viewAny', NaturalPerson::class);

        $filePath = storage_path('app/public/templates/Plantilla_Personas_Naturales.xlsx');

        if (!file_exists($filePath)) {
            if (!is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            return redirect()->back()->withErrors(['La plantilla de personas naturales no se encuentra disponible en el servidor.']);
        }

        return response()->download($filePath, 'Plantilla_Importacion_Personas_Naturales.xlsx');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'dni' => ['nullable', 'string', 'min:8', 'max:10', 'regex:/^\d{8,10}$/'],
            'nombres' => ['nullable', 'string', 'max:255'],
            'apellido_paterno' => ['nullable', 'string', 'max:255'],
            'apellido_materno' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
