<?php

namespace App\Http\Controllers;

use App\Http\Requests\Charge\CreateChargeRequest;
use App\Http\Requests\Charge\RejectChargeRequest;
use App\Http\Requests\Charge\UpdateChargeRequest;
use App\Http\Requests\Charge\DeleteChargeRequest;
use App\Http\Requests\Charge\SignChargeRequest;
use App\Models\Charge;
use App\Services\ChargeService;
use App\Services\ChargeReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChargeController extends Controller
{
    public function __construct(
        protected ChargeService $service,
        protected ChargeReportService $reportService
    ) {
    }

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

    public function update(UpdateChargeRequest $request, Charge $charge)
    {
        $data = $request->validated();
        $data['user'] = $request->user();
        $this->service->update($data, $charge->id);

        return redirect()->route('charges.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy(DeleteChargeRequest $request, Charge $charge)
    {
        $data = $request->validated();
        $data['user'] = $request->user();
        $this->service->delete($data, $charge->id);

        return redirect()->route('charges.index')->with('success', 'Cargo eliminado correctamente.');
    }

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

        return redirect()->route('charges.index')->with('success', 'Cargo rechazado correctamente.');
    }

    public function getSignature(Charge $charge)
    {
        if (!$charge->signature?->signature_root)
            abort(404);

        $path = $charge->signature->signature_root;
        if (!\Storage::disk('local')->exists($path))
            abort(404);

        $content = \Storage::disk('local')->get($path);

        return response($content)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function getEvidence(Charge $charge)
    {
        if (!$charge->signature?->evidence_root)
            abort(404);

        $path = $charge->signature->evidence_root;
        if (!\Storage::disk('local')->exists($path))
            abort(404);

        return response()->file(\Storage::disk('local')->path($path));
    }

    public function getCartaPoder(Charge $charge)
    {
        if (!$charge->signature?->carta_poder_path)
            abort(404);

        $path = $charge->signature->carta_poder_path;
        if (!\Storage::disk('local')->exists($path))
            abort(404);

        return response()->file(\Storage::disk('local')->path($path));
    }

    // Reportes delegados al ReportService
    public function reportSent(Request $request)
    {
        $filters = $this->getSearchFilters($request);
        return $this->reportService->getSentReport($filters['sent'], $request->user(), $filters['default_period']);
    }

    public function reportCreated(Request $request)
    {
        $filters = $this->getSearchFilters($request);
        return $this->reportService->getCreatedReport($filters['created'], $request->user(), $filters['default_period']);
    }

    public function reportResolution(Request $request)
    {
        $filters = $this->getSearchFilters($request);
        return $this->reportService->getResolutionReport($filters['resolucion'], $request->user(), $filters['default_period']);
    }

    public function reportReceived(Request $request)
    {
        $filters = $this->getSearchFilters($request);
        return $this->reportService->getReceivedReport($filters['received'], $request->user(), $filters['default_period']);
    }

    private function getSearchFilters($request)
    {
        $defaultPeriod = \App\Models\Setting::getValue('charge_period', '');
        $defaultPeriod = $defaultPeriod !== '' ? $defaultPeriod : null;

        return [
            'sent' => [
                'search' => $request->input('search'),
                'signature_status' => $request->input('signature_status'),
                'period' => $request->input('period', $defaultPeriod),
            ],
            'received' => [
                'search' => $request->input('received_search'),
                'signature_status' => $request->input('received_signature_status'),
                'period' => $request->input('received_period', $defaultPeriod),
            ],
            'resolucion' => [
                'search' => $request->input('resolution_search'),
                'signature_status' => $request->input('resolution_signature_status'),
                'period' => $request->input('resolution_period', $defaultPeriod),
            ],
            'created' => [
                'search' => $request->input('created_search'),
                'signature_status' => $request->input('created_signature_status'),
                'period' => $request->input('created_period', $defaultPeriod),
            ],
            'user' => $request->user(),
            'default_period' => $defaultPeriod,
        ];
    }
}
