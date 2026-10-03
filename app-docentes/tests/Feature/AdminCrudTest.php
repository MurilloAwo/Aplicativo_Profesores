<?php

namespace Tests\Feature;

use App\Models\Asignatura;
use App\Models\CriterioAcreditacion;
use App\Models\EntidadExterna;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\ProgramaCurricular;
use App\Models\TipoActividad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $profesor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->profesor = User::factory()->profesor()->create();
    }

    public function test_profesor_recibe_403_en_todas_las_rutas_de_administracion(): void
    {
        $rutas = [
            '/admin',
            '/admin/usuarios',
            '/admin/usuarios/create',
            '/admin/periodos',
            '/admin/programas',
            '/admin/asignaturas',
            '/admin/grupos',
            '/admin/tipos-actividad',
            '/admin/entidades',
            '/admin/criterios',
        ];

        foreach ($rutas as $ruta) {
            $this->actingAs($this->profesor)->get($ruta)->assertForbidden();
        }
    }

    public function test_admin_puede_ver_dashboard_con_metricas(): void
    {
        $this->actingAs($this->admin)->get('/admin')
            ->assertOk()
            ->assertSee('Panel de Administración del Departamento')
            ->assertSee('Docentes');
    }

    public function test_admin_crud_usuarios_y_toggle_activo(): void
    {
        // 1. Crear nuevo docente
        $response = $this->actingAs($this->admin)->post(route('admin.usuarios.store'), [
            'nombres' => 'Mario',
            'apellidos' => 'Linares',
            'documento' => '12345678',
            'email' => 'mlinares@unal.edu.co',
            'password' => 'password123',
            'rol' => User::ROL_PROFESOR,
            'tipo_vinculacion' => 'planta',
            'dedicacion' => 'Dedicación Exclusiva',
            'categoria' => 'Asociado',
        ]);

        $response->assertRedirect(route('admin.usuarios.index'));
        $nuevo = User::where('email', 'mlinares@unal.edu.co')->first();
        $this->assertNotNull($nuevo);
        $this->assertSame('Mario Linares', $nuevo->name);
        $this->assertTrue($nuevo->activo);

        // 2. Editar docente
        $this->actingAs($this->admin)->put(route('admin.usuarios.update', $nuevo), [
            'nombres' => 'Mario Alberto',
            'apellidos' => 'Linares Vásquez',
            'documento' => '12345678',
            'email' => 'mlinares@unal.edu.co',
            'rol' => User::ROL_PROFESOR,
            'tipo_vinculacion' => 'planta',
            'dedicacion' => 'Dedicación Exclusiva',
            'categoria' => 'Titular',
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertSame('Mario Alberto Linares Vásquez', $nuevo->fresh()->name);
        $this->assertSame('Titular', $nuevo->fresh()->categoria);

        // 3. Desactivar usuario
        $this->actingAs($this->admin)->patch(route('admin.usuarios.toggle-activo', $nuevo))
            ->assertRedirect();
        $this->assertFalse($nuevo->fresh()->activo);

        // 4. Admin no puede desactivarse a sí mismo
        $this->actingAs($this->admin)->patch(route('admin.usuarios.toggle-activo', $this->admin))
            ->assertRedirect();
        $this->assertTrue($this->admin->fresh()->activo);
    }

    public function test_admin_crud_periodos_y_regla_de_periodo_activo_unico(): void
    {
        $p1 = PeriodoAcademico::factory()->activo()->create(['codigo' => '2026-1']);
        $p2 = PeriodoAcademico::factory()->create(['codigo' => '2026-2', 'activo' => false]);

        $this->assertTrue($p1->fresh()->activo);
        $this->assertFalse($p2->fresh()->activo);

        // Activar P2 debe desactivar P1
        $this->actingAs($this->admin)->patch(route('admin.periodos.activar', $p2))
            ->assertRedirect();

        $this->assertFalse($p1->fresh()->activo);
        $this->assertTrue($p2->fresh()->activo);
        $this->assertSame(1, PeriodoAcademico::where('activo', true)->count());

        // Toggle cerrado / reabierto
        $this->assertFalse($p2->fresh()->cerrado);
        $this->actingAs($this->admin)->patch(route('admin.periodos.toggle-cerrado', $p2))
            ->assertRedirect();
        $this->assertTrue($p2->fresh()->cerrado);
    }

    public function test_admin_crud_programas_y_asignaturas(): void
    {
        // 1. Programa
        $response = $this->actingAs($this->admin)->post(route('admin.programas.store'), [
            'codigo' => '3000',
            'nombre' => 'Maestría en Analítica',
            'nivel' => 'posgrado',
        ]);
        $response->assertRedirect(route('admin.programas.index'));
        $prog = ProgramaCurricular::where('codigo', '3000')->first();
        $this->assertNotNull($prog);

        // 2. Asignatura
        $response = $this->actingAs($this->admin)->post(route('admin.asignaturas.store'), [
            'codigo' => '5001',
            'nombre' => 'Minería de Datos',
            'creditos' => 4,
            'tipologia' => 'Elegible',
            'programa_curricular_id' => $prog->id,
        ]);
        $response->assertRedirect(route('admin.asignaturas.index'));
        $asig = Asignatura::where('codigo', '5001')->first();
        $this->assertNotNull($asig);

        // No se puede borrar programa si tiene asignaturas
        $this->actingAs($this->admin)->delete(route('admin.programas.destroy', $prog))
            ->assertRedirect();
        $this->assertModelExists($prog);

        // Borrar asignatura
        $this->actingAs($this->admin)->delete(route('admin.asignaturas.destroy', $asig))
            ->assertRedirect();
        $this->assertModelMissing($asig);

        // Ahora sí se puede borrar el programa
        $this->actingAs($this->admin)->delete(route('admin.programas.destroy', $prog))
            ->assertRedirect();
        $this->assertModelMissing($prog);
    }

    public function test_admin_crud_grupos_y_asignacion_a_profesor(): void
    {
        $prog = ProgramaCurricular::factory()->create();
        $asig = Asignatura::factory()->create(['programa_curricular_id' => $prog->id]);
        $periodo = PeriodoAcademico::factory()->activo()->create(['codigo' => '2026-2']);

        $response = $this->actingAs($this->admin)->post(route('admin.grupos.store'), [
            'asignatura_id' => $asig->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $this->profesor->id,
            'numero_grupo' => '1',
            'modalidad' => 'presencial',
            'horario' => 'Lun-Mié 10:00-12:00',
            'numero_estudiantes' => 30,
        ]);

        $response->assertRedirect();
        $grupo = Grupo::where('asignatura_id', $asig->id)->where('numero_grupo', '1')->first();
        $this->assertNotNull($grupo);
        $this->assertSame($this->profesor->id, $grupo->profesor_id);

        // Validación de unicidad de grupo en la misma asignatura y periodo
        $dupResponse = $this->actingAs($this->admin)->post(route('admin.grupos.store'), [
            'asignatura_id' => $asig->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $this->profesor->id,
            'numero_grupo' => '1',
            'modalidad' => 'virtual',
            'horario' => 'Vie 08:00-12:00',
            'numero_estudiantes' => 25,
        ]);
        $dupResponse->assertSessionHasErrors('numero_grupo');
    }

    public function test_admin_crud_tipos_actividad_entidades_y_criterios(): void
    {
        // 1. Tipo de actividad
        $this->actingAs($this->admin)->post(route('admin.tipos-actividad.store'), [
            'nombre' => 'Hackathon o Maratón de Programación',
            'descripcion' => 'Competencia de desarrollo en tiempo limitado',
            'categoria' => 'extension',
            'requiere_entidad_externa' => 1,
            'activo' => 1,
        ])->assertRedirect();

        $tipo = TipoActividad::where('nombre', 'Hackathon o Maratón de Programación')->first();
        $this->assertNotNull($tipo);
        $this->assertTrue($tipo->requiere_entidad_externa);

        // Toggle activo
        $this->actingAs($this->admin)->patch(route('admin.tipos-actividad.toggle-activo', $tipo))
            ->assertRedirect();
        $this->assertFalse($tipo->fresh()->activo);

        // 2. Entidad externa
        $this->actingAs($this->admin)->post(route('admin.entidades.store'), [
            'nombre' => 'Microsoft Colombia',
            'nit' => '860123456-7',
            'sector' => 'Tecnología',
            'ciudad' => 'Bogotá',
            'contacto' => 'universidades@microsoft.com',
        ])->assertRedirect();

        $entidad = EntidadExterna::where('nit', '860123456-7')->first();
        $this->assertNotNull($entidad);

        $this->actingAs($this->admin)->delete(route('admin.entidades.destroy', $entidad))
            ->assertRedirect();
        $this->assertModelMissing($entidad);

        // 3. Criterio de acreditación (catálogo editable)
        $this->actingAs($this->admin)->post(route('admin.criterios.store'), [
            'codigo' => 'FACTOR-05',
            'nombre' => 'Visibilidad nacional e internacional',
            'descripcion' => 'Impacto y presencia externa de profesores y estudiantes',
        ])->assertRedirect();

        $crit = CriterioAcreditacion::where('codigo', 'FACTOR-05')->first();
        $this->assertNotNull($crit);

        $this->actingAs($this->admin)->delete(route('admin.criterios.destroy', $crit))
            ->assertRedirect();
        $this->assertModelMissing($crit);
    }
}
