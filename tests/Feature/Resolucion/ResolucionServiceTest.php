<?php

namespace Tests\Feature\Resolucion;

use App\Models\LevelModality;
use App\Models\Resolucion;
use App\Models\Setting;
use App\Models\User;
use App\Services\Resolucion\ResolucionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResolucionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ResolucionService $service;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ResolucionService::class);
        $this->user = User::factory()->create();

        Setting::create(['key' => 'charge_period', 'value' => '2026']);
    }

    /** @test */
    public function it_creates_a_resolucion_without_automatic_charge()
    {
        $data = [
            'rd' => 'RD-001-2026',
            'fecha' => '2026-03-01',
            'asunto' => 'Resolucion de prueba',
            'nombres_apellidos' => 'Juan Perez',
            'dni' => '12345678',
            'user_id' => $this->user->id,
        ];

        $result = $this->service->create($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('resolucions', ['rd' => 'RD-001-2026']);
        $resolucion = Resolucion::where('rd', 'RD-001-2026')->first();

        $this->assertDatabaseMissing('charges', [
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseMissing('charge_resolucion', [
            'resolucion_id' => $resolucion->id,
        ]);
    }

    /** @test */
    public function it_calculates_stats_correctly()
    {
        Resolucion::create([
            'rd' => 'RD-001',
            'fecha' => '2026-01-01',
            'periodo' => '2026',
            'asunto' => 'A',
            'nombres_apellidos' => 'B',
            'dni' => 'C',
        ]);

        $filters = [
            'search' => null,
            'periodo' => '2026',
        ];

        $result = $this->service->getAll($filters);

        $this->assertEquals(1, $result['totalResolucionesPeriodo']);
        $this->assertEquals('2026', $result['chargePeriod']);
    }

    /** @test */
    public function it_creates_a_resolucion_with_level_modality()
    {
        $modality = LevelModality::create(['name' => 'MODALIDAD TEST']);

        $data = [
            'rd' => 'RD-002-2026',
            'fecha' => '2026-03-01',
            'asunto' => 'Resolucion de prueba con modalidad',
            'user_id' => $this->user->id,
            'level_modality_id' => $modality->id,
            'interesados' => [
                [
                    'type' => 'Persona Natural',
                    'nombres' => 'Juan',
                    'apellido_paterno' => 'Perez',
                    'apellido_materno' => 'Gomez',
                    'dni' => '87654321',
                ],
            ],
        ];

        $result = $this->service->create($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('resolucions', [
            'rd' => 'RD-002-2026',
            'level_modality_id' => $modality->id,
        ]);
    }

    /** @test */
    public function it_filters_resoluciones_by_rd_and_asunto()
    {
        Resolucion::create([
            'rd' => 'RD-ABC-123',
            'fecha' => '2026-01-01',
            'periodo' => '2026',
            'asunto' => 'NOMBRAMIENTO DE PERSONAL DOCENTE',
            'nombres_apellidos' => 'Juan Perez',
            'dni' => '12345678',
        ]);

        Resolucion::create([
            'rd' => 'RD-XYZ-789',
            'fecha' => '2026-01-02',
            'periodo' => '2026',
            'asunto' => 'CESACIÓN DE FUNCIONES DE ADMINISTRATIVO',
            'nombres_apellidos' => 'Maria Lopez',
            'dni' => '87654321',
        ]);

        // Filtrar por RD que coincide con la primera
        $resultsRd = $this->service->getAll(['search_rd' => 'ABC']);
        $this->assertEquals(1, $resultsRd['resoluciones']->total());
        $this->assertEquals('RD-ABC-123', $resultsRd['resoluciones']->first()->rd);

        // Filtrar por Asunto que coincide con la segunda
        $resultsAsunto = $this->service->getAll(['search_asunto' => 'CESACIÓN']);
        $this->assertEquals(1, $resultsAsunto['resoluciones']->total());
        $this->assertEquals('RD-XYZ-789', $resultsAsunto['resoluciones']->first()->rd);

        // Filtrar por combinación que no coincide con ninguna
        $resultsCombined = $this->service->getAll([
            'search_rd' => 'ABC',
            'search_asunto' => 'CESACIÓN',
        ]);
        $this->assertEquals(0, $resultsCombined['resoluciones']->total());
    }
}
