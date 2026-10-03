<?php

namespace Tests\Feature;

use App\Models\Actividad;
use App\Models\Asignatura;
use App\Models\CriterioAcreditacion;
use App\Models\EntidadExterna;
use App\Models\Evidencia;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\ProgramaCurricular;
use App\Models\TipoActividad;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_puebla_los_datos_esperados_y_es_idempotente(): void
    {
        // Primera ejecución del seeder
        $this->seed(DatabaseSeeder::class);

        // Verificaciones cuantitativas exactas
        $this->assertSame(4, User::count(), 'Deben existir 4 usuarios (1 admin + 3 profesores)');
        $this->assertSame(1, User::where('rol', User::ROL_ADMIN)->count(), 'Debe haber exactamente 1 admin');
        $this->assertSame(3, User::where('rol', User::ROL_PROFESOR)->count(), 'Deben haber exactamente 3 profesores');

        $this->assertSame(2, PeriodoAcademico::count(), 'Deben existir exactamente 2 periodos');
        $this->assertSame(1, PeriodoAcademico::where('activo', true)->count(), 'Solo debe haber 1 periodo activo');
        $this->assertSame(1, PeriodoAcademico::where('cerrado', true)->count(), 'Debe haber 1 periodo cerrado');

        $this->assertSame(2, ProgramaCurricular::count(), 'Deben existir 2 programas curriculares');
        $this->assertSame(6, Asignatura::count(), 'Deben existir 6 asignaturas');

        $this->assertGreaterThanOrEqual(6, Grupo::count(), 'Deben existir al menos 6 grupos');
        $this->assertGreaterThanOrEqual(9, TipoActividad::count(), 'Deben existir al menos 9 tipos de actividad');

        // Regla estricta: catálogo de criterios de acreditación vacío por defecto
        $this->assertSame(0, CriterioAcreditacion::count(), 'Criterios de acreditación debe iniciar vacío');

        // Verificar que los profesores tengan grupos en el periodo activo
        $periodoActivo = PeriodoAcademico::actual();
        $this->assertNotNull($periodoActivo);

        $profesores = User::where('rol', User::ROL_PROFESOR)->get();
        foreach ($profesores as $prof) {
            $gruposActivos = $prof->grupos()->where('periodo_academico_id', $periodoActivo->id)->count();
            $this->assertGreaterThan(0, $gruposActivos, "El profesor {$prof->email} debe tener grupos activos");
        }

        // Idempotencia: segunda ejecución sin duplicaciones ni errores de clave única
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(4, User::count());
        $this->assertSame(2, PeriodoAcademico::count());
        $this->assertSame(2, ProgramaCurricular::count());
        $this->assertSame(6, Asignatura::count());
        $this->assertSame(0, CriterioAcreditacion::count());
    }

    public function test_todos_los_factories_generan_modelos_validos(): void
    {
        $user = User::factory()->profesor()->create();
        $admin = User::factory()->admin()->create();
        $programa = ProgramaCurricular::factory()->create();
        $periodo = PeriodoAcademico::factory()->activo()->create(['codigo' => '2030-1']);
        $asignatura = Asignatura::factory()->create(['programa_curricular_id' => $programa->id]);
        $grupo = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $user->id,
        ]);
        $tipo = TipoActividad::factory()->create();
        $entidad = EntidadExterna::factory()->create();
        $actividad = Actividad::factory()->conEntidad()->create([
            'grupo_id' => $grupo->id,
            'tipo_actividad_id' => $tipo->id,
            'entidad_externa_id' => $entidad->id,
        ]);
        $evidencia = Evidencia::factory()->create(['actividad_id' => $actividad->id]);
        $criterio = CriterioAcreditacion::factory()->create();

        $this->assertModelExists($user);
        $this->assertModelExists($admin);
        $this->assertModelExists($programa);
        $this->assertModelExists($periodo);
        $this->assertModelExists($asignatura);
        $this->assertModelExists($grupo);
        $this->assertModelExists($tipo);
        $this->assertModelExists($entidad);
        $this->assertModelExists($actividad);
        $this->assertModelExists($evidencia);
        $this->assertModelExists($criterio);
    }
}
