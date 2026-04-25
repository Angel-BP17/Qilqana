<?php

namespace Tests\Feature\Charge;

use App\Models\Charge;
use App\Models\Setting;
use App\Models\User;
use App\Services\Charge\ChargeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function it_deletes_charge_and_removes_physical_files()
    {
        Storage::fake('local');

        $charge = Charge::create([
            'n_charge' => '1',
            'user_id' => $this->user->id,
            'asunto' => 'Cargo a eliminar',
            'tipo_interesado' => 'Persona Natural',
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
