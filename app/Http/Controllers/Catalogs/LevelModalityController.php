<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelModalityRequest;
use App\Http\Requests\UpdateLevelModalityRequest;
use App\Models\LevelModality;
use App\Services\Resolucion\LevelModalityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LevelModalityController extends Controller
{
    public function __construct(protected LevelModalityService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('level-modalities.index', [
            'modalities' => $this->service->getAll(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLevelModalityRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->back()->with('success', 'Modalidad/Nivel creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLevelModalityRequest $request, LevelModality $levelModality): RedirectResponse
    {
        $this->service->update($request->validated(), $levelModality);

        return redirect()->back()->with('success', 'Modalidad/Nivel actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LevelModality $levelModality): RedirectResponse
    {
        // Verificar si tiene resoluciones asociadas
        if ($levelModality->resolucions()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar la modalidad/nivel porque tiene resoluciones asociadas.');
        }

        $this->service->delete($levelModality);

        return redirect()->back()->with('success', 'Modalidad/Nivel eliminado correctamente.');
    }
}
