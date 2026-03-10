<?php

namespace Tests\Feature;

use App\Models\Charge;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChargeFileServiceTest extends TestCase
{
    /**
     * Verifica que la ruta de firma sirve el contenido SVG correctamente.
     */
    public function test_it_serves_signature_svg_correctly()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        
        $charge = Charge::factory()->create(['user_id' => $user->id]);
        $path = "private/charges_signatures/test_signature_{$charge->id}.svg";
        $svgContent = '<svg>signature</svg>';
        
        Storage::disk('local')->put($path, $svgContent);
        
        $charge->signature()->create([
            'signature_root' => $path,
            'signature_status' => 'firmado'
        ]);

        $response = $this->actingAs($user)->get(route('charges.file.signature', $charge));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertEquals($svgContent, $response->getContent());
    }

    /**
     * Verifica que la ruta de evidencia sirve el archivo físico.
     */
    public function test_it_serves_evidence_file()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        
        $charge = Charge::factory()->create(['user_id' => $user->id]);
        $path = "private/charges_evidence/test_evidence_{$charge->id}.jpg";
        
        Storage::disk('local')->put($path, 'fake-image-content');
        
        $charge->signature()->create([
            'evidence_root' => $path,
            'signature_status' => 'firmado'
        ]);

        $response = $this->actingAs($user)->get(route('charges.file.evidence', $charge));

        $response->assertStatus(200);
    }

    /**
     * Verifica que devuelve 404 si el registro existe pero el archivo no.
     */
    public function test_it_returns_404_if_file_missing_on_disk()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        
        $charge = Charge::factory()->create(['user_id' => $user->id]);
        
        $charge->signature()->create([
            'signature_root' => 'path/to/nowhere.svg',
            'signature_status' => 'firmado'
        ]);

        $response = $this->actingAs($user)->get(route('charges.file.signature', $charge));

        $response->assertStatus(404);
    }
}
