<?php

namespace Tests\Feature;

use App\Models\Charge;
use App\Models\Resolucion;
use App\Models\User;
use App\Models\Setting;
use App\Services\ResolucionService;
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
    public function it_creates_a_resolucion_and_associated_charge()
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
        $this->assertDatabaseHas('charges', [
            'resolucion_id' => Resolucion::where('rd', 'RD-001-2026')->first()->id,
            'user_id' => $this->user->id,
            'charge_period' => '2026'
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
            'dni' => 'C'
        ]);

        $filters = [
            'search' => null,
            'periodo' => '2026'
        ];

        $result = $this->service->getAll($filters);

        $this->assertEquals(1, $result['totalResolucionesPeriodo']);
        $this->assertEquals('2026', $result['chargePeriod']);
    }
}
