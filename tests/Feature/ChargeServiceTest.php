<?php

namespace Tests\Feature;

use App\Models\Charge;
use App\Models\User;
use App\Models\Setting;
use App\Services\ChargeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChargeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ChargeService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ChargeService::class);
        $this->user = User::factory()->create();
        
        // Configuración inicial de periodos
        Setting::create(['key' => 'charge_period', 'value' => '2026']);
    }

    /** @test */
    public function it_can_create_a_charge_and_clears_cache()
    {
        Cache::shouldReceive('forget')->atLeast()->once();
        
        $data = [
            'user' => $this->user,
            'tipo_interesado' => 'Trabajador UGEL',
            'asunto' => 'Prueba de cargo',
            'document_date' => now()->toDateString(),
            'assigned_to' => $this->user->id,
        ];

        $result = $this->service->create($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('charges', [
            'user_id' => $this->user->id,
            'asunto' => 'Prueba de cargo',
            'charge_period' => '2026'
        ]);
    }

    /** @test */
    public function it_caches_all_charges_index()
    {
        $data = [
            'user' => $this->user,
            'sent' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'received' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'resolucion' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'created' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'default_period' => '2026'
        ];

        // Primera llamada: debe ejecutar consultas (no hay caché)
        $result1 = $this->service->getAll($data);
        
        // Crear un nuevo cargo manualmente para ver si la caché lo ignora en la segunda llamada
        Charge::create([
            'n_charge' => '999',
            'user_id' => $this->user->id,
            'asunto' => 'Ignorado por caché',
            'charge_period' => '2026',
            'tipo_interesado' => 'Persona Natural'
        ]);

        // Segunda llamada: debe venir de la caché (no verá el nuevo cargo)
        $result2 = $this->service->getAll($data);

        $this->assertEquals($result1['sentCharges']->count(), $result2['sentCharges']->count());
        $this->assertFalse($result2['sentCharges']->contains('asunto', 'Ignorado por caché'));
    }

    /** @test */
    public function it_deletes_charge_and_removes_physical_files()
    {
        Storage::fake('local');
        
        $charge = Charge::create([
            'n_charge' => '1',
            'user_id' => $this->user->id,
            'asunto' => 'Cargo a eliminar',
            'tipo_interesado' => 'Persona Natural'
        ]);

        $signature = $charge->signature()->create([
            'signature_status' => 'firmado',
            'signature_root' => 'private/charges_signatures/test.svg',
            'evidence_root' => 'private/charges_evidence/test.jpg',
        ]);

        Storage::disk('local')->put('private/charges_signatures/test.svg', '<svg></svg>');
        Storage::disk('local')->put('private/charges_evidence/test.jpg', 'fake content');

        $this->service->delete(['user' => $this->user], $charge->id);

        $this->assertDatabaseMissing('charges', ['id' => $charge->id]);
        Storage::disk('local')->assertMissing('private/charges_signatures/test.svg');
        Storage::disk('local')->assertMissing('private/charges_evidence/test.jpg');
    }
}
