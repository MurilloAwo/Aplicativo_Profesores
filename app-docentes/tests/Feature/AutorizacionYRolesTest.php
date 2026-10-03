<?php

namespace Tests\Feature;

use App\Models\Actividad;
use App\Models\Asignatura;
use App\Models\Evidencia;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\TipoActividad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AutorizacionYRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:admin'])->get('/test-ruta-admin', fn () => 'admin_ok');
        Route::middleware(['web', 'auth', 'role:profesor'])->get('/test-ruta-profesor', fn () => 'profesor_ok');
        Route::middleware(['web', 'auth', 'role:admin,profesor'])->get('/test-ruta-ambos', fn () => 'ambos_ok');
    }

    public function test_middleware_role_permite_o_bloquea_segun_rol(): void
    {
        $admin = User::factory()->admin()->create();
        $profesor = User::factory()->profesor()->create();

        // Admin en ruta exclusiva de admin -> 200
        $this->actingAs($admin)->get('/test-ruta-admin')
            ->assertOk()
            ->assertSee('admin_ok');

        // Profesor en ruta exclusiva de admin -> 403
        $this->actingAs($profesor)->get('/test-ruta-admin')
            ->assertForbidden();

        // Profesor en ruta de profesor -> 200
        $this->actingAs($profesor)->get('/test-ruta-profesor')
            ->assertOk()
            ->assertSee('profesor_ok');

        // Admin en ruta de profesor -> 403
        $this->actingAs($admin)->get('/test-ruta-profesor')
            ->assertForbidden();

        // Ambos en ruta compartida -> 200
        $this->actingAs($admin)->get('/test-ruta-ambos')->assertOk();
        $this->actingAs($profesor)->get('/test-ruta-ambos')->assertOk();
    }

    public function test_usuario_inactivo_es_desautenticado_y_redirigido(): void
    {
        $inactivo = User::factory()->inactivo()->create();

        $response = $this->actingAs($inactivo)->get('/dashboard');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_grupo_policy_restringe_acceso_entre_profesores(): void
    {
        $admin = User::factory()->admin()->create();
        $prof1 = User::factory()->profesor()->create();
        $prof2 = User::factory()->profesor()->create();

        $periodo = PeriodoAcademico::factory()->activo()->create(['codigo' => '2026-2']);
        $asignatura = Asignatura::factory()->create();

        $grupo1 = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $prof1->id,
            'numero_grupo' => '1',
        ]);

        $grupo2 = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $prof2->id,
            'numero_grupo' => '2',
        ]);

        // Profesor 1 puede ver su grupo, pero no el de Profesor 2
        $this->assertTrue($prof1->can('view', $grupo1));
        $this->assertFalse($prof1->can('view', $grupo2));

        // Profesor no puede crear ni editar grupos (solo admin)
        $this->assertFalse($prof1->can('create', Grupo::class));
        $this->assertFalse($prof1->can('update', $grupo1));

        // Admin puede ver y editar cualquier grupo
        $this->assertTrue($admin->can('view', $grupo1));
        $this->assertTrue($admin->can('view', $grupo2));
        $this->assertTrue($admin->can('update', $grupo1));
    }

    public function test_actividad_policy_restringe_entre_profesores_y_bloquea_periodos_cerrados(): void
    {
        $admin = User::factory()->admin()->create();
        $prof1 = User::factory()->profesor()->create();
        $prof2 = User::factory()->profesor()->create();

        $periodoActivo = PeriodoAcademico::factory()->activo()->create(['codigo' => '2026-2']);
        $periodoCerrado = PeriodoAcademico::factory()->cerrado()->create(['codigo' => '2026-1']);

        $tipo = TipoActividad::factory()->create();

        $grupoActivoProf1 = Grupo::factory()->create([
            'periodo_academico_id' => $periodoActivo->id,
            'profesor_id' => $prof1->id,
        ]);
        $grupoCerradoProf1 = Grupo::factory()->create([
            'periodo_academico_id' => $periodoCerrado->id,
            'profesor_id' => $prof1->id,
        ]);
        $grupoActivoProf2 = Grupo::factory()->create([
            'periodo_academico_id' => $periodoActivo->id,
            'profesor_id' => $prof2->id,
        ]);

        $actividadProf1 = Actividad::factory()->create([
            'grupo_id' => $grupoActivoProf1->id,
            'tipo_actividad_id' => $tipo->id,
        ]);

        $actividadCerradaProf1 = Actividad::factory()->create([
            'grupo_id' => $grupoCerradoProf1->id,
            'tipo_actividad_id' => $tipo->id,
        ]);

        // Profesor 1 puede ver y editar su actividad en periodo activo
        $this->assertTrue($prof1->can('view', $actividadProf1));
        $this->assertTrue($prof1->can('update', $actividadProf1));
        $this->assertTrue($prof1->can('create', [Actividad::class, $grupoActivoProf1]));

        // Profesor 1 NO puede editar actividades en periodo cerrado (solo lectura)
        $this->assertTrue($prof1->can('view', $actividadCerradaProf1));
        $this->assertFalse($prof1->can('update', $actividadCerradaProf1));
        $this->assertFalse($prof1->can('create', [Actividad::class, $grupoCerradoProf1]));
        $this->assertFalse($prof1->can('delete', $actividadCerradaProf1));

        // Profesor 1 NO puede ver ni editar actividades de Profesor 2
        $this->assertFalse($prof1->can('create', [Actividad::class, $grupoActivoProf2]));

        // Admin puede ver cualquier actividad
        $this->assertTrue($admin->can('view', $actividadProf1));
        $this->assertTrue($admin->can('view', $actividadCerradaProf1));
    }

    public function test_evidencia_policy_asociada_a_actividad_y_periodo(): void
    {
        $admin = User::factory()->admin()->create();
        $prof1 = User::factory()->profesor()->create();
        $prof2 = User::factory()->profesor()->create();

        $periodoActivo = PeriodoAcademico::factory()->activo()->create(['codigo' => '2026-2']);
        $periodoCerrado = PeriodoAcademico::factory()->cerrado()->create(['codigo' => '2026-1']);

        $grupoActivo = Grupo::factory()->create([
            'periodo_academico_id' => $periodoActivo->id,
            'profesor_id' => $prof1->id,
        ]);
        $grupoCerrado = Grupo::factory()->create([
            'periodo_academico_id' => $periodoCerrado->id,
            'profesor_id' => $prof1->id,
        ]);

        $actividadActiva = Actividad::factory()->create(['grupo_id' => $grupoActivo->id]);
        $actividadCerrada = Actividad::factory()->create(['grupo_id' => $grupoCerrado->id]);

        $evidenciaActiva = Evidencia::factory()->create(['actividad_id' => $actividadActiva->id]);
        $evidenciaCerrada = Evidencia::factory()->create(['actividad_id' => $actividadCerrada->id]);

        // Profesor 1 ve evidencia de su actividad activa y puede subir nuevas
        $this->assertTrue($prof1->can('view', $evidenciaActiva));
        $this->assertTrue($prof1->can('create', [Evidencia::class, $actividadActiva]));
        $this->assertTrue($prof1->can('delete', $evidenciaActiva));

        // En periodo cerrado, profesor ve la evidencia pero no puede agregar ni borrar
        $this->assertTrue($prof1->can('view', $evidenciaCerrada));
        $this->assertFalse($prof1->can('create', [Evidencia::class, $actividadCerrada]));
        $this->assertFalse($prof1->can('delete', $evidenciaCerrada));

        // Profesor 2 no puede ver ni manipular evidencias de Profesor 1
        $this->assertFalse($prof2->can('view', $evidenciaActiva));
        $this->assertFalse($prof2->can('create', [Evidencia::class, $actividadActiva]));
        $this->assertFalse($prof2->can('delete', $evidenciaActiva));

        // Admin puede ver cualquier evidencia
        $this->assertTrue($admin->can('view', $evidenciaActiva));
        $this->assertTrue($admin->can('view', $evidenciaCerrada));
    }

    public function test_menu_de_navegacion_muestra_roles_y_textos_en_espanol(): void
    {
        $profesor = User::factory()->profesor()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($profesor)->get('/dashboard')
            ->assertOk()
            ->assertSee('Panel Principal')
            ->assertSee('Profesor')
            ->assertSee('Mi Perfil')
            ->assertSee('Cerrar Sesión');

        $this->actingAs($admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('Panel Principal')
            ->assertSee('Administrador');
    }
}
