<?php

namespace Tests\Feature;

use App\Exports\ConsolidadoDepartamentoExport;
use App\Models\Actividad;
use App\Models\Asignatura;
use App\Models\CriterioAcreditacion;
use App\Models\Evidencia;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\ProgramaCurricular;
use App\Models\TipoActividad;
use App\Models\User;
use App\Services\ConsolidadoDepartamentoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ConsolidadoAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $profesor1;
    private User $profesor2;
    private PeriodoAcademico $periodo;
    private TipoActividad $tipo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->profesor1 = User::factory()->profesor()->create([
            'nombres' => 'Carlos',
            'apellidos' => 'Zapata',
            'name' => 'Carlos Zapata',
        ]);
        $this->profesor2 = User::factory()->profesor()->create([
            'nombres' => 'Ana',
            'apellidos' => 'Rendón',
            'name' => 'Ana Rendón',
        ]);

        $this->periodo = PeriodoAcademico::factory()->activo()->create([
            'codigo' => '2026-1',
        ]);

        $programa = ProgramaCurricular::factory()->create([
            'nombre' => 'Ingeniería de Sistemas e Informática',
        ]);

        $asig1 = Asignatura::factory()->create([
            'programa_curricular_id' => $programa->id,
            'nombre' => 'Bases de Datos',
        ]);
        $asig2 = Asignatura::factory()->create([
            'programa_curricular_id' => $programa->id,
            'nombre' => 'Redes de Computadores',
        ]);

        $grupo1 = Grupo::factory()->create([
            'asignatura_id' => $asig1->id,
            'periodo_academico_id' => $this->periodo->id,
            'profesor_id' => $this->profesor1->id,
            'numero_grupo' => 1,
            'numero_estudiantes' => 30,
        ]);

        $grupo2 = Grupo::factory()->create([
            'asignatura_id' => $asig2->id,
            'periodo_academico_id' => $this->periodo->id,
            'profesor_id' => $this->profesor2->id,
            'numero_grupo' => 1,
            'numero_estudiantes' => 25,
        ]);

        $this->tipo = TipoActividad::factory()->create([
            'nombre' => 'Visita Técnica Industrial',
            'categoria' => 'extension',
        ]);

        // Actividades del profesor 1
        $act1 = Actividad::factory()->create([
            'grupo_id' => $grupo1->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Visita a Datacenter Claro',
            'duracion_horas' => 4.0,
            'numero_estudiantes_participantes' => 28,
            'estado' => 'registrada',
        ]);
        Evidencia::factory()->create(['actividad_id' => $act1->id]);

        // Actividades del profesor 2
        $act2 = Actividad::factory()->create([
            'grupo_id' => $grupo2->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Laboratorio en Vivo de Enrutamiento BGP',
            'duracion_horas' => 2.5,
            'numero_estudiantes_participantes' => 24,
            'estado' => 'registrada',
        ]);
        Evidencia::factory()->create(['actividad_id' => $act2->id]);
    }

    public function test_admin_puede_ver_consolidado_departamental_en_pantalla(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.consolidado.index', [
            'periodo_id' => $this->periodo->id,
        ]));

        $response->assertOk();
        $response->assertSee('Consolidado Departamental de Actividades Docentes');
        $response->assertSee('Carlos Zapata');
        $response->assertSee('Ana Rendón');
        $response->assertSee('Visita Técnica Industrial');
        $response->assertSee(route('admin.consolidado.pdf', ['periodo_id' => $this->periodo->id]));
        $response->assertSee(route('admin.consolidado.excel', ['periodo_id' => $this->periodo->id]));
    }

    public function test_servicio_consolida_correctamente_metricas_departamentales(): void
    {
        $criterio = CriterioAcreditacion::factory()->create(['codigo' => 'C-02', 'nombre' => 'Impacto']);
        Actividad::first()->criterios()->attach($criterio->id);

        $service = app(ConsolidadoDepartamentoService::class);
        $consolidado = $service->obtenerConsolidadoPeriodo($this->periodo);

        $this->assertEquals(2, $consolidado['totales']['docentes_activos']);
        $this->assertEquals(2, $consolidado['totales']['grupos_ofertados']);
        $this->assertEquals(2, $consolidado['totales']['asignaturas_distintas']);
        $this->assertEquals(2, $consolidado['totales']['actividades']);
        $this->assertEquals(2, $consolidado['totales']['registradas']);
        $this->assertEquals(6.5, $consolidado['totales']['horas']);
        $this->assertEquals(52, $consolidado['totales']['estudiantes']);
        $this->assertEquals(2, $consolidado['totales']['evidencias']);

        $this->assertCount(2, $consolidado['por_docente']);
        $this->assertCount(1, $consolidado['criterios']);
        $this->assertEquals('C-02', $consolidado['criterios']->first()['codigo']);
    }

    public function test_admin_puede_descargar_consolidado_departamental_en_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.consolidado.pdf', [
            'periodo_id' => $this->periodo->id,
        ]));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Consolidado_Departamento_2026-1.pdf', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_admin_puede_descargar_consolidado_departamental_en_excel(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->get(route('admin.consolidado.excel', [
            'periodo_id' => $this->periodo->id,
        ]));

        $response->assertOk();

        Excel::assertDownloaded('Consolidado_Departamento_2026-1.xlsx', function (ConsolidadoDepartamentoExport $export) {
            $sheets = $export->sheets();

            return count($sheets) === 3;
        });
    }

    public function test_profesor_recibe_403_al_intentar_acceder_al_consolidado_administrativo(): void
    {
        $response = $this->actingAs($this->profesor1)->get(route('admin.consolidado.index'));
        $response->assertForbidden();

        $resPdf = $this->actingAs($this->profesor1)->get(route('admin.consolidado.pdf', ['periodo_id' => $this->periodo->id]));
        $resPdf->assertForbidden();

        $resExcel = $this->actingAs($this->profesor1)->get(route('admin.consolidado.excel', ['periodo_id' => $this->periodo->id]));
        $resExcel->assertForbidden();
    }
}
