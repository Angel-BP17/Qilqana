<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResolucionTypeRequest;
use App\Http\Requests\UpdateResolucionTypeRequest;
use App\Models\ResolucionType;
use App\Services\Resolucion\ResolucionTypeService;

class ResolucionTypeController extends Controller
{
    public function __construct(protected ResolucionTypeService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('resolucion-types.index', [
            'types' => $this->service->getAll(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResolucionTypeRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->back()->with('success', 'Tipo de resolución creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResolucionTypeRequest $request, ResolucionType $resolucionType)
    {
        $this->service->update($request->validated(), $resolucionType);

        return redirect()->back()->with('success', 'Tipo de resolución actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResolucionType $resolucionType)
    {
        // Verificar si tiene resoluciones asociadas
        if ($resolucionType->resolucions()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar el tipo porque tiene resoluciones asociadas.');
        }

        $this->service->delete($resolucionType);

        return redirect()->back()->with('success', 'Tipo de resolución eliminado correctamente.');
    }
}
