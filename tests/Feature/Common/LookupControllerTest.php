<?php

namespace Tests\Feature\Common;

use App\Models\NaturalPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LookupControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_person_from_database_if_exists()
    {
        NaturalPerson::create([
            'dni' => '12345678',
            'nombres' => 'JUAN',
            'apellido_paterno' => 'PEREZ',
            'apellido_materno' => 'GOMEZ',
        ]);

        $response = $this->getJson('/api/natural-people/by-dni/12345678');

        $response->assertStatus(200)
            ->assertJsonPath('data.nombres', 'JUAN');
    }

    /** @test */
    public function it_calls_external_api_if_not_in_database()
    {
        config(['services.apisperu.key' => 'fake-key']);

        Http::fake([
            'dniruc.apisperu.com/*' => Http::response([
                'dni' => '87654321',
                'nombres' => 'MARIA',
                'apellidoPaterno' => 'LOPEZ',
                'apellidoMaterno' => 'DIAZ',
            ], 200),
        ]);

        $response = $this->getJson('/api/natural-people/by-dni/87654321');

        $response->assertStatus(200)
            ->assertJsonPath('data.nombres', 'MARIA');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer fake-key');
        });
    }

    /** @test */
    public function it_returns_404_if_api_key_missing_and_not_in_db()
    {
        config(['services.apisperu.key' => '']);

        $response = $this->getJson('/api/natural-people/by-dni/00000000');

        $response->assertStatus(404);
    }
}
