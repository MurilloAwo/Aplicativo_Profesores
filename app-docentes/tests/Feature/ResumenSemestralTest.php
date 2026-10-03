<?php

namespace Tests\Feature;

use App\Exports\ResumenSemestralExport;
use App\Models\Actividad;
use App\Models\Asignatura;
use App\Models\CriterioAcreditacion;
use App\Models\Evidencia;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\TipoActividad;
use App\Models\User;
use App\Services\ResumenAcademicoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ResumenSemestralTest extends TestCase
{
    use RefreshDatabase;

    private User $profesor;
    private PeriodoAcademico $periodo;
    private Grupo $grupo1;
    private Grupo $grupo2;
    private TipoActividad $tipo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profesor = User::factory()->profesor()->create([
            'nombres' => 'Sebastián',
            'apellidos' => 'Gómez',
            'name' => 'Sebastián Gómez',
        ]);

        $this->periodo = PeriodoAcademico::factory()->activo()->create([
            'codigo' => '2026-1',
        ]);

        $asignatura1 = Asignatura::factory()->create([
            'codigo' => '3008123',
            'nombre' => 'Ingeniería de Software II',
        ]);

        $asignatura2 = Asignatura::factory()->create([
            'codigo' => '3008124',
            'nombre' => 'Arquitectura de Software',
        ]);

        $this->grupo1 = Grupo::factory()->create([
            'asignatura_id' => $asignatura1->id,
            'periodo_academico_id' => $this->periodo->id,
            'profesor_id' => $this->profesor->id,
            'numero_grupo' => 1,
            'numero_estudiantes' => 35,
        ]);

        $this->grupo2 = Grupo::factory()->create([
            'asignatura_id' => $asignatura2->id,
            'periodo_academico_id' => $this->periodo->id,
            'profesor_id' => $this->profesor->id,
            'numero_grupo' => 2,
            'numero_estudiantes' => 28,
        ]);

        $this->tipo = TipoActividad::factory()->create([
            'nombre' => 'Charla Magistral',
            'categoria' => 'extension',
        ]);
    }

    public function test_profesor_puede_ver_resumen_semestral_en_pantalla(): void
    {
        $actividad = Actividad::factory()->create([
            'grupo_id' => $this->grupo1->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Charla sobre Microservicios en Producción',
            'duracion_horas' => 3.5,
            'numero_estudiantes_participantes' => 32,
            'estado' => 'registrada',
        ]);

        $response = $this->actingAs($this->profesor)->get(route('resumen.index', [
            'periodo_id' => $this->periodo->id,
        ]));

        $response->assertOk();
        $response->assertSee('Resumen Semestral de Actividades');
        $response->assertSee('Ingeniería de Software II');
        $response->assertSee('Charla sobre Microservicios en Producción');
        $response->assertSee('3.5');
        $response->assertSee(route('resumen.pdf', ['periodo_id' => $this->periodo->id]));
        $response->assertSee(route('resumen.excel', ['periodo_id' => $this->periodo->id]));
    }

    public function test_servicio_resumen_academico_calcula_metricas_correctas(): void
    {
        $criterio = CriterioAcreditacion::factory()->create(['codigo' => 'C-01', 'nombre' => 'Pertinencia']);

        $act1 = Actividad::factory()->create([
            'grupo_id' => $this->grupo1->id,
            'tipo_actividad_id' => $this->tipo->id,
            'duracion_horas' => 4.0,
            'numero_estudiantes_participantes' => 30,
            'estado' => 'registrada',
        ]);
        $act1->criterios()->attach($criterio->id);

        Evidencia::factory()->create(['actividad_id' => $act1->id]);
        Evidencia::factory()->create(['actividad_id' => $act1->id]);

        $act2 = Actividad::factory()->create([
            'grupo_id' => $this->grupo2->id,
            'tipo_actividad_id' => $this->tipo->id,
            'duracion_horas' => 2.0,
            'numero_estudiantes_participantes' => 25,
            'estado' => 'borrador',
        ]);

        $service = app(ResumenAcademicoService::class);
        $resumen = $service->obtenerResumenProfesor($this->profesor, $this->periodo);

        $this->assertEquals(2, $resumen['totales']['materias']);
        $this->assertEquals(2, $resumen['totales']['grupos']);
        $this->assertEquals(2, $resumen['totales']['actividades']);
        $this->assertEquals(1, $resumen['totales']['registradas']);
        $this->assertEquals(1, $resumen['totales']['borrador']);
        $this->assertEquals(6.0, $resumen['totales']['horas']);
        $this->assertEquals(55, $resumen['totales']['estudiantes']);
        $this->assertEquals(2, $resumen['totales']['evidencias']);

        $this->assertCount(1, $resumen['criterios']);
        $this->assertEquals('C-01', $resumen['criterios']->first()['codigo']);
        $this->assertEquals(1, $resumen['criterios']->first()['cantidad_actividades']);
    }

    public function test_profesor_puede_descargar_resumen_en_pdf(): void
    {
        Actividad::factory()->create([
            'grupo_id' => $this->grupo1->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Taller de Pruebas Unitarias',
        ]);

        $response = $this->actingAs($this->profesor)->get(route('resumen.pdf', [
            'periodo_id' => $this->periodo->id,
        ]));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Resumen_Docente_2026-1', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_profesor_puede_descargar_resumen_en_excel_con_multiples_hojas(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->profesor)->get(route('resumen.excel', [
            'periodo_id' => $this->periodo->id,
        ]));

        $response->assertOk();

        Excel::assertDownloaded('Resumen_Docente_2026-1_sebastian-gomez.xlsx', function (ResumenSemestralExport $export) {
            $sheets = $export->sheets();

            return count($sheets) === 3;
        });
    }
}
