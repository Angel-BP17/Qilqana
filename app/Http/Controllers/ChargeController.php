<?php

namespace App\Http\Controllers;

use App\Http\Requests\Charge\CreateChargeRequest;
use App\Http\Requests\Charge\RejectChargeRequest;
use App\Http\Requests\Charge\UpdateChargeRequest;
use App\Http\Requests\Charge\DeleteChargeRequest;
use App\Http\Requests\Charge\SignChargeRequest;
use App\Models\Charge;
use App\Services\ChargeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChargeController extends Controller
{
    public function __construct(protected ChargeService $service)
    {
    }

    // CRUD Operations

    public function index(Request $request)
    {
        return view('charges.index', $this->service->getAll($this->getSearchFilters($request)));
    }

    public function refresh(Request $request)
    {
        return view('charges.partials.dashboard', $this->service->getAll($this->getSearchFilters($request)));
    }

    public function store(CreateChargeRequest $request)
    {
        $data = $request->validated();
        $data['user'] = $request->user();

        $this->service->create($data);

        return redirect()->route('charges.index')->with('success', 'Cargo creado correctamente.');
    }

    public function show(Charge $charge)
    {
        return view('charges.show', compact('charge'));
    }

    public function update(UpdateChargeRequest $request, Charge $charge)
    {
        $data = $request->validated();
        $data['user'] = $request->user();

        $this->service->update($data, $charge->id);

        return redirect()->route('charges.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy(DeleteChargeRequest $request, Charge $charge)
    {
        Log::info('ChargeController.destroy:start', [
            'charge_id' => $charge->id,
            'user_id' => $request->user()?->id,
            'method' => $request->method(),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
        ]);

        $data = $request->validated();
        $data['user'] = $request->user();

        $this->service->delete($data, $charge->id);

        Log::info('ChargeController.destroy:success', [
            'charge_id' => $charge->id,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()->route('charges.index')->with('success', 'Cargo eliminado correctamente.');
    }
    //------------------------------------------------------------------------------------------------------------------------------------------------/

    // Firmar cargo

    public function signStore(SignChargeRequest $request, Charge $charge)
    {
        $data = $request->validated();

        $files = [
            'carta_poder' => $request->file('carta_poder'),
            'evidence_root' => $request->file('evidence_root'),
        ];

        $this->service->signStore($data, $files, $charge->id, $request->user()->id);

        return redirect()->route('charges.index')->with('success', 'Cargo firmado correctamente.');
    }

    public function reject(RejectChargeRequest $request, Charge $charge)
    {
        $data = $request->validated();

        $this->service->reject($data, $charge->id, $request->user()->id);

        return redirect()->route('charges.index')->with('success', 'Cargo rechazado y ocultado de la bandeja de recibidos.');
    }
    //------------------------------------------------------------------------------------------------------------------------------------------------/

    // Reportes

    public function reportSent(Request $request)
    {
        $criteria = $this->getSearchFilters($request)['sent'];

        return $this->service->getReportSentData($criteria, $request->user(), $this->getSearchFilters($request)['default_period']);
    }

    public function reportCreated(Request $request)
    {

        return $this->service->getReportCreatedData($this->getSearchFilters($request)['created'], $request->user(), $this->getSearchFilters($request)['default_period']);
    }

    public function reportResolution(Request $request)
    {
        return $this->service->getReportResolutionData($this->getSearchFilters($request)['resolucion'], $request->user(), $this->getSearchFilters($request)['default_period']);
    }

    public function reportReceived(Request $request)
    {
        return $this->service->getReportReceivedData($this->getSearchFilters($request)['received'], $request->user(), $this->getSearchFilters($request)['default_period']);
    }
    //------------------------------------------------------------------------------------------------------------------------------------------------/

    //Métodos privados

    private function getSearchFilters($request)
    {
        // Período asignado en configuración
        $defaultPeriod = $this->service->getChargePeriod();
        $sentPeriod = $this->service->normalizePeriod($request->input('period', $defaultPeriod));
        $receivedPeriod = $this->service->normalizePeriod($request->input('received_period', $defaultPeriod));
        $resolutionPeriod = $this->service->normalizePeriod($request->input('resolution_period', $defaultPeriod));
        $createdPeriod = $this->service->normalizePeriod($request->input('created_period', $defaultPeriod));

        // Filtros de búsqueda, estado y periodo
        return [
            'sent' => [
                'search' => $request->input('search', null),
                'signature_status' => $request->input('signature_status', null),
                // Período de cargos enviados
                'period' => $sentPeriod,
            ],
            'received' => [
                'search' => $request->input('received_search', null),
                'signature_status' => $request->input('received_signature_status', null),
                // Período de cargos recibidos
                'period' => $receivedPeriod,
            ],
            'resolucion' => [
                'search' => $request->input('resolution_search', null),
                'signature_status' => $request->input('resolution_signature_status', null),
                // Periodo de cargos de resolución
                'period' => $resolutionPeriod,
            ],
            'created' => [
                'search' => $request->input('created_search', null),
                'signature_status' => $request->input('created_signature_status', null),
                // Periodo de cargos creados
                'period' => $createdPeriod,
            ],
            // Solo se muestran los cargos del usuario autenticado
            'user' => $request->user(),
            'default_period' => $defaultPeriod,
        ];
    }
}
