<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsuntoType\StoreAsuntoTypeRequest;
use App\Http\Requests\AsuntoType\UpdateAsuntoTypeRequest;
use App\Models\ResolucionType;
use App\Services\Asunto\AsuntoTypeService;

class AsuntoTypeController extends Controller
{
    public function __construct(protected AsuntoTypeService $service)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $asuntoTypes = $this->service->getAll();
        $resolucionTypes = ResolucionType::orderBy('name')->get();

        return view('asunto-types.index', compact('asuntoTypes', 'resolucionTypes'));
    }

    public function store(StoreAsuntoTypeRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('asunto-types.index')
            ->with('success', 'Tipo de asunto creado correctamente.');
    }

    public function update(UpdateAsuntoTypeRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->route('asunto-types.index')
            ->with('success', 'Tipo de asunto actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);

            return redirect()->route('asunto-types.index')
                ->with('success', 'Tipo de asunto eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('asunto-types.index')
                ->with('error', 'No se puede eliminar el tipo de asunto porque está siendo utilizado.');
        }
    }
}
