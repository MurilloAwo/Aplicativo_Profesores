<?php

namespace Tests\Feature;

use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\ProgramaCurricular;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisMateriasTest extends TestCase
{
    use RefreshDatabase;

    private User $profesor1;

    private User $profesor2;

    private PeriodoAcademico $periodoActivo;

    private Grupo $grupoProf1;

    private Grupo $grupoProf2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profesor1 = User::factory()->profesor()->create();
        $this->profesor2 = User::factory()->profesor()->create();

        $this->periodoActivo = PeriodoAcademico::factory()->activo()->create(['codigo' => '2026-2']);

        $programa = ProgramaCurricular::factory()->create();
        $asig1 = Asignatura::factory()->create(['nombre' => 'Ingeniería de Software II', 'programa_curricular_id' => $programa->id]);
        $asig2 = Asignatura::factory()->create(['nombre' => 'Inteligencia Artificial', 'programa_curricular_id' => $programa->id]);

        $this->grupoProf1 = Grupo::factory()->create([
            'asignatura_id' => $asig1->id,
            'periodo_academico_id' => $this->periodoActivo->id,
            'profesor_id' => $this->profesor1->id,
            'numero_grupo' => '1',
            'numero_estudiantes' => 35,
        ]);

        $this->grupoProf2 = Grupo::factory()->create([
            'asignatura_id' => $asig2->id,
            'periodo_academico_id' => $this->periodoActivo->id,
            'profesor_id' => $this->profesor2->id,
            'numero_grupo' => '2',
            'numero_estudiantes' => 28,
        ]);
    }

    public function test_profesor_ve_sus_materias_del_periodo_activo(): void
    {
        $response = $this->actingAs($this->profesor1)->get(route('materias.index'));

        $response->assertOk()
            ->assertSee('Mis Materias y Grupos Asignados')
            ->assertSee('Ingeniería de Software II')
            ->assertSee('Grupo 1')
            ->assertDontSee('Inteligencia Artificial'); // Materia del profesor 2
    }

    public function test_profesor_puede_ver_detalle_de_su_grupo(): void
    {
        $response = $this->actingAs($this->profesor1)->get(route('materias.show', $this->grupoProf1));

        $response->assertOk()
            ->assertSee('Ingeniería de Software II')
            ->assertSee('Grupo 1')
            ->assertSee('35'); // 35 estudiantes inscritos
    }

    public function test_profesor_recibe_403_al_acceder_a_grupo_de_otro_profesor(): void
    {
        $response = $this->actingAs($this->profesor1)->get(route('materias.show', $this->grupoProf2));

        $response->assertForbidden();
    }

    public function test_muestra_indicador_de_periodo_cerrado(): void
    {
        $periodoCerrado = PeriodoAcademico::factory()->cerrado()->create(['codigo' => '2026-1']);
        $grupoCerrado = Grupo::factory()->create([
            'periodo_academico_id' => $periodoCerrado->id,
            'profesor_id' => $this->profesor1->id,
        ]);

        $response = $this->actingAs($this->profesor1)->get(route('materias.show', $grupoCerrado));

        $response->assertOk()
            ->assertSee('Periodo Cerrado (Solo Lectura)');
    }
}
