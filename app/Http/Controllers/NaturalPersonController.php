<?php

namespace App\Http\Controllers;

use App\Http\Requests\NaturalPerson\ImportNaturalPersonRequest;
use App\Imports\NaturalPeopleImport;
use App\Models\NaturalPerson;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NaturalPersonController extends Controller
{
    public function index()
    {
        $this->ensureAllowed(request()->user(), ['modulo personas naturales', 'natural-people.view']);

        $naturalPeople = NaturalPerson::orderByDesc('created_at')->paginate(10);

        return view('natural-people.index', compact('naturalPeople'));
    }

    public function store(Request $request)
    {
        $this->ensureAllowed($request->user(), ['natural-people.create']);

        $data = $this->validateData($request);
        NaturalPerson::create($data);

        return redirect()->route('natural-people.index')->with('success', 'Persona natural creada correctamente');
    }

    public function update(Request $request, NaturalPerson $naturalPerson)
    {
        $this->ensureAllowed($request->user(), ['natural-people.edit']);

        $data = $this->validateData($request);
        $naturalPerson->update($data);

        return redirect()->route('natural-people.index')->with('success', 'Persona natural actualizada correctamente');
    }

    public function destroy(Request $request, NaturalPerson $naturalPerson)
    {
        $this->ensureAllowed($request->user(), ['natural-people.delete']);

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
        $this->ensureAllowed($request->user(), ['natural-people.create']);

        $updateExisting = $request->boolean('update_existing', true);
        Excel::import(new NaturalPeopleImport($updateExisting), $request->file('archivo_excel'));

        return redirect()->route('natural-people.index')->with('success', 'Personas naturales importadas correctamente');
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
