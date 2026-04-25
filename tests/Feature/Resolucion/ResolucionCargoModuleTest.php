<?php

namespace Tests\Feature\Resolucion;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Charge;
use App\Models\Resolucion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResolucionCargoModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operador;

    protected function setUp(): void
    {
        parent::setUp();

        // Deshabilitar CSRF para tests
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // Setup de Roles y Permisos
        $adminRole = Role::create(['name' => 'ADMINISTRADOR']);
        Permission::create(['name' => 'modulo resoluciones']);
        Permission::create(['name' => 'modulo cargos']);
        $adminRole->givePermissionTo(['modulo resoluciones', 'modulo cargos']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole($adminRole);

        $this->operador = User::factory()->create();

        // Configuración base necesaria para que no falle el index
        Setting::create(['key' => 'charge_period', 'value' => '2026']);
        Setting::create(['key' => 'charges_refresh_interval', 'value' => '5']);

        Storage::fake('local');
    }

    private function getFullFilters()
    {
        return [
            'sent' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'received' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'resolucion' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'created' => ['period' => '2026', 'search' => null, 'signature_status' => null],
            'default_period' => '2026',
        ];
    }

    /**
     * ETAPA 1: CONTRATO DE VISTA (BLADE PROPS)
     */
    public function test_stage_1_charge_index_view_contract()
    {
        $this->withoutExceptionHandling();
        // Forzamos parámetros para evitar Error 500 por falta de llaves en el array
        $response = $this->actingAs($this->admin)->get(route('charges.index', [
            'period' => '2026',
            'received_period' => '2026',
            'resolution_period' => '2026',
            'created_period' => '2026',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('charges.index');
    }

    /**
     * ETAPA 2: VALIDACIÓN (GATEKEEPING)
     */
    public function test_stage_2_resolucion_validation_rules()
    {
        // El FormRequest de Resolucion espera estos campos.
        // Si fallan, debe redirigir de vuelta con errores.
        $response = $this->actingAs($this->admin)->post(route('resolucions.store'), [
            'rd' => '',
            'fecha' => 'invalida',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['rd', 'fecha', 'asunto', 'nombres', 'apellido_paterno', 'apellido_materno', 'dni']);
    }

    /**
     * ETAPA 3: SEGURIDAD Y ROLES (RBAC)
     */
    public function test_stage_3_operator_cannot_access_resolution_reports()
    {
        // La ruta correcta es charges.reports.resolution
        $response = $this->actingAs($this->operador)->get(route('charges.reports.resolution'));

        $response->assertStatus(403);
    }

    /**
     * ETAPA 4: INTEGRIDAD DE MODELOS (RELACIONES)
     */
    public function test_stage_4_creating_resolucion_automatically_creates_charge_and_signature()
    {
        $data = [
            'rd' => 'RD-2026-001',
            'fecha' => '2026-03-09',
            'asunto' => 'Nombramiento Test',
            'nombres' => 'Juan',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Gomez',
            'dni' => '77777777',
            'user_id' => $this->admin->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('resolucions.store'), $data);
        $response->assertStatus(302);

        $resolucion = Resolucion::where('rd', 'RD-2026-001')->first();

        $this->assertNotNull($resolucion, 'La resolución no se creó en la base de datos.');
        $this->assertDatabaseHas('charges', [
            'resolucion_id' => $resolucion->id,
            'user_id' => $this->admin->id,
            'charge_period' => '2026',
        ]);

        $charge = Charge::where('resolucion_id', $resolucion->id)->first();
        $this->assertDatabaseHas('signatures', [
            'charge_id' => $charge->id,
            'signature_status' => 'pendiente',
        ]);
    }

    /**
     * ETAPA 5: REGLAS DE NEGOCIO (SERVICE LAYER)
     */
    public function test_stage_5_charge_number_increment_logic()
    {
        // Crear primer cargo manualmente
        Charge::factory()->create([
            'user_id' => $this->admin->id,
            'n_charge' => '1',
            'charge_period' => '2026',
        ]);

        // Al crear el segundo vía controlador, el servicio debe asignar '2'
        $response = $this->actingAs($this->admin)->post(route('charges.store'), [
            'tipo_interesado' => 'Trabajador UGEL',
            'asunto' => 'Segundo Cargo',
            'document_date' => '2026-03-10',
            'assigned_to' => $this->admin->id,
            'dni' => '12345678',
            'nombres' => 'Juan',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Gomez',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('charges', [
            'user_id' => $this->admin->id,
            'asunto' => 'Segundo Cargo',
            'n_charge' => '2',
            'charge_period' => '2026',
        ]);
    }

    /**
     * ETAPA 6: MANEJO DE ARCHIVOS (STORAGE)
     */
    public function test_stage_6_sign_charge_stores_files_correctly()
    {
        $charge = Charge::factory()->create(['user_id' => $this->admin->id]);
        $charge->signature()->create([
            'signature_status' => 'pendiente',
            'assigned_to' => $this->admin->id,
        ]);

        $evidence = UploadedFile::fake()->image('evidencia.jpg');

        // La ruta es charges.sign.store y el método es PUT
        $response = $this->actingAs($this->admin)->put(route('charges.sign.store', $charge), [
            'firma' => '<svg>test</svg>',
            'titularidad' => '1',
            'evidence_root' => $evidence,
        ]);

        $response->assertStatus(302);

        $charge->refresh();
        $this->assertEquals('firmado', $charge->signature->signature_status);
        Storage::disk('local')->assertExists($charge->signature->signature_root);
        Storage::disk('local')->assertExists($charge->signature->evidence_root);
    }

    /**
     * ETAPA 7: RESILIENCIA (EDGE CASES)
     */
    public function test_stage_7_search_with_special_characters()
    {
        $this->withoutExceptionHandling();
        Charge::factory()->create([
            'asunto' => 'Resolución con Ñandú y tildes áéíóú',
            'user_id' => $this->admin->id,
            'charge_period' => '2026',
        ]);

        $response = $this->actingAs($this->admin)->get(route('charges.index', ['search' => 'Ñandú']));

        $response->assertStatus(200);
        $response->assertSee('Ñandú');
    }

    /**
     * ETAPA 8: RENDIMIENTO (N+1 QUERIES)
     */
    public function test_stage_8_charge_index_eager_loading()
    {
        // Crear varios registros con relaciones
        Charge::factory()->count(5)->create(['user_id' => $this->admin->id]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($this->admin)->get(route('charges.index'));

        $queries = DB::getQueryLog();

        // Un número razonable de queries para el index (eager loading de user, signature, etc)
        // No debe ser > 25 (base de laravel + las tablas cargadas + settings)
        $this->assertLessThan(25, count($queries), 'Se detectaron posibles consultas N+1 en el index de cargos.');
    }
}
