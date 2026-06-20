<?php

namespace App\Http\Controllers\Catalogs;

use App\Filters\NaturalPersonFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\NaturalPerson\ImportNaturalPersonRequest;
use App\Imports\NaturalPeopleImport;
use App\Models\NaturalPerson;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        // Transformar a mayúsculas
        $data['nombres'] = mb_strtoupper($data['nombres'], 'UTF-8');
        $data['apellido_paterno'] = mb_strtoupper($data['apellido_paterno'], 'UTF-8');
        $data['apellido_materno'] = mb_strtoupper($data['apellido_materno'], 'UTF-8');

        NaturalPerson::create($data);

        return redirect()->route('natural-people.index')->with('success', 'Persona natural creada correctamente');
    }

    public function update(Request $request, NaturalPerson $naturalPerson)
    {
        $this->authorize('update', $naturalPerson);

        $data = $this->validateData($request, $naturalPerson->id);

        // Transformar a mayúsculas
        $data['nombres'] = mb_strtoupper($data['nombres'], 'UTF-8');
        $data['apellido_paterno'] = mb_strtoupper($data['apellido_paterno'], 'UTF-8');
        $data['apellido_materno'] = mb_strtoupper($data['apellido_materno'], 'UTF-8');

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

        if (! file_exists($filePath)) {
            if (! is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            return redirect()->back()->withErrors(['La plantilla de personas naturales no se encuentra disponible en el servidor.']);
        }

        return response()->download($filePath, 'Plantilla_Importacion_Personas_Naturales.xlsx');
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'dni' => [
                'required_without:cedula',
                'nullable',
                'string',
                'min:8',
                'max:10',
                'regex:/^\d{8,10}$/',
                Rule::unique('natural_people', 'dni')->ignore($ignoreId),
            ],
            'cedula' => [
                'required_without:dni',
                'nullable',
                'string',
                'max:255',
                Rule::unique('natural_people', 'cedula')->ignore($ignoreId),
            ],
            'nombres' => ['required', 'string', 'max:255'],
            'apellido_paterno' => ['required', 'string', 'max:255'],
            'apellido_materno' => ['required', 'string', 'max:255'],
        ], [
            'dni.required_without' => 'El DNI es obligatorio si no ingresa una Cédula.',
            'cedula.required_without' => 'La Cédula es obligatoria si no ingresa un DNI.',
            'dni.unique' => 'Ya existe una persona registrada con este DNI.',
            'cedula.unique' => 'Ya existe una persona registrada con esta cédula.',
            'nombres.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_materno.required' => 'El apellido materno es obligatorio.',
        ]);
    }
}
