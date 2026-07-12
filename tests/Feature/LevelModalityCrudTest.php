<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\LevelModality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LevelModalityCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);

        // Limpiar cache de permisos
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Crear el rol si no existe
        if (! Role::where('name', 'ADMINISTRADOR')->exists()) {
            Role::create(['name' => 'ADMINISTRADOR']);
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('ADMINISTRADOR');
    }

    /** @test */
    public function an_admin_can_access_level_modalities_index()
    {
        $response = $this->actingAs($this->admin)->get(route('level-modalities.index'));
        $response->assertStatus(200);
        $response->assertViewIs('level-modalities.index');
    }

    /** @test */
    public function an_admin_can_create_a_level_modality()
    {
        $data = [
            'name' => 'SUPERIOR',
            'description' => 'Educacion superior tecnologica',
        ];

        $response = $this->actingAs($this->admin)->post(route('level-modalities.store'), $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('level_modalities', ['name' => 'SUPERIOR']);
    }

    /** @test */
    public function an_admin_can_update_a_level_modality()
    {
        $modality = LevelModality::create(['name' => 'ANTIGUA MODALIDAD']);

        $data = [
            'name' => 'NUEVO NOMBRE MODALIDAD',
            'description' => 'Descripcion actualizada',
        ];

        $response = $this->actingAs($this->admin)->put(route('level-modalities.update', $modality), $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('level_modalities', ['name' => 'NUEVO NOMBRE MODALIDAD']);
    }

    /** @test */
    public function an_admin_can_delete_a_level_modality_without_resolutions()
    {
        $modality = LevelModality::create(['name' => 'BORRAR MODALIDAD']);

        $response = $this->actingAs($this->admin)->delete(route('level-modalities.destroy', $modality));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('level_modalities', ['id' => $modality->id]);
    }
}
