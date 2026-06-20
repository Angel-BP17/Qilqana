<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\ResolucionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ResolucionTypeCrudTest extends TestCase
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
    public function an_admin_can_access_resolucion_types_index()
    {
        $response = $this->actingAs($this->admin)->get(route('resolucion-types.index'));
        $response->assertStatus(200);
        $response->assertViewIs('resolucion-types.index');
    }

    /** @test */
    public function an_admin_can_create_a_resolucion_type()
    {
        $data = [
            'name' => 'DIRECTORAL',
            'description' => 'Resoluciones emitidas por el director',
        ];

        $response = $this->actingAs($this->admin)->post(route('resolucion-types.store'), $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('resolucion_types', ['name' => 'DIRECTORAL']);
    }

    /** @test */
    public function an_admin_can_update_a_resolucion_type()
    {
        $type = ResolucionType::create(['name' => 'ANTIGUO']);

        $data = [
            'name' => 'NUEVO NOMBRE',
            'description' => 'Descripcion actualizada',
        ];

        $response = $this->actingAs($this->admin)->put(route('resolucion-types.update', $type), $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('resolucion_types', ['name' => 'NUEVO NOMBRE']);
    }

    /** @test */
    public function an_admin_can_delete_a_resolucion_type_without_resolutions()
    {
        $type = ResolucionType::create(['name' => 'BORRAR']);

        $response = $this->actingAs($this->admin)->delete(route('resolucion-types.destroy', $type));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('resolucion_types', ['id' => $type->id]);
    }
}
