<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EsquemaBaseDatosTest extends TestCase
{
    use RefreshDatabase;

    public static function tablasYColumnas(): array
    {
        return [
            'users' => ['users', ['id', 'name', 'nombres', 'apellidos', 'documento', 'email', 'rol',
                'tipo_vinculacion', 'dedicacion', 'categoria', 'activo', 'password', 'created_at', 'updated_at']],
            'programas_curriculares' => ['programas_curriculares', ['id', 'codigo', 'nombre', 'nivel']],
            'periodos_academicos' => ['periodos_academicos', ['id', 'codigo', 'fecha_inicio', 'fecha_fin', 'activo', 'cerrado']],
            'asignaturas' => ['asignaturas', ['id', 'codigo', 'nombre', 'creditos', 'tipologia', 'programa_curricular_id']],
            'grupos' => ['grupos', ['id', 'asignatura_id', 'periodo_academico_id', 'profesor_id', 'numero_grupo',
                'modalidad', 'horario', 'numero_estudiantes']],
            'tipos_actividad' => ['tipos_actividad', ['id', 'nombre', 'descripcion', 'categoria', 'requiere_entidad_externa', 'activo']],
            'entidades_externas' => ['entidades_externas', ['id', 'nombre', 'nit', 'sector', 'ciudad', 'contacto']],
            'actividades' => ['actividades', ['id', 'grupo_id', 'tipo_actividad_id', 'entidad_externa_id', 'titulo',
                'descripcion', 'objetivo', 'fecha_inicio', 'fecha_fin', 'duracion_horas', 'lugar', 'modalidad',
                'numero_estudiantes_participantes', 'nombre_invitado', 'cargo_invitado', 'resultados_obtenidos',
                'observaciones', 'estado', 'created_at', 'updated_at', 'deleted_at']],
            'actividad_grupo' => ['actividad_grupo', ['actividad_id', 'grupo_id']],
            'evidencias' => ['evidencias', ['id', 'actividad_id', 'tipo', 'nombre_original', 'ruta', 'mime', 'tamano']],
            'criterios_acreditacion' => ['criterios_acreditacion', ['id', 'codigo', 'nombre', 'descripcion']],
            'actividad_criterio' => ['actividad_criterio', ['actividad_id', 'criterio_acreditacion_id']],
        ];
    }

    /** @dataProvider tablasYColumnas */
    public function test_la_tabla_existe_con_sus_columnas(string $tabla, array $columnas): void
    {
        $this->assertTrue(Schema::hasTable($tabla), "No existe la tabla {$tabla}");
        $this->assertTrue(
            Schema::hasColumns($tabla, $columnas),
            "Faltan columnas en {$tabla}: ".implode(', ', array_diff($columnas, Schema::getColumnListing($tabla)))
        );
    }

    public function test_el_grupo_es_unico_por_asignatura_periodo_y_numero(): void
    {
        $ids = $this->crearDatosBase();
        $grupo = [
            'asignatura_id' => $ids['asignatura'],
            'periodo_academico_id' => $ids['periodo'],
            'profesor_id' => $ids['profesor'],
            'numero_grupo' => '1',
        ];
        DB::table('grupos')->insert($grupo);

        $this->expectException(QueryException::class);
        DB::table('grupos')->insert($grupo);
    }

    public function test_el_codigo_de_periodo_es_unico(): void
    {
        $this->crearDatosBase();

        $this->expectException(QueryException::class);
        DB::table('periodos_academicos')->insert([
            'codigo' => '2026-2', 'fecha_inicio' => '2026-08-01', 'fecha_fin' => '2026-12-15',
        ]);
    }

    public function test_una_actividad_cubre_varios_grupos_y_criterios(): void
    {
        $ids = $this->crearDatosBase();
        $g1 = DB::table('grupos')->insertGetId([
            'asignatura_id' => $ids['asignatura'], 'periodo_academico_id' => $ids['periodo'],
            'profesor_id' => $ids['profesor'], 'numero_grupo' => '1',
        ]);
        $g2 = DB::table('grupos')->insertGetId([
            'asignatura_id' => $ids['asignatura'], 'periodo_academico_id' => $ids['periodo'],
            'profesor_id' => $ids['profesor'], 'numero_grupo' => '2',
        ]);
        $tipo = DB::table('tipos_actividad')->insertGetId(['nombre' => 'Visita empresarial', 'categoria' => 'relacion_sector_externo']);
        $actividad = DB::table('actividades')->insertGetId([
            'grupo_id' => $g1, 'tipo_actividad_id' => $tipo, 'titulo' => 'Visita',
            'descripcion' => 'Descripción', 'fecha_inicio' => '2026-09-10',
        ]);
        DB::table('actividad_grupo')->insert([
            ['actividad_id' => $actividad, 'grupo_id' => $g1],
            ['actividad_id' => $actividad, 'grupo_id' => $g2],
        ]);
        $criterio = DB::table('criterios_acreditacion')->insertGetId(['codigo' => 'C-1', 'nombre' => 'Criterio de prueba']);
        DB::table('actividad_criterio')->insert(['actividad_id' => $actividad, 'criterio_acreditacion_id' => $criterio]);

        $this->assertSame(2, DB::table('actividad_grupo')->where('actividad_id', $actividad)->count());
        $this->assertSame('borrador', DB::table('actividades')->find($actividad)->estado);

        // La pivote no admite el mismo grupo dos veces.
        $this->expectException(QueryException::class);
        DB::table('actividad_grupo')->insert(['actividad_id' => $actividad, 'grupo_id' => $g1]);
    }

    public function test_el_catalogo_de_criterios_inicia_vacio(): void
    {
        $this->assertSame(0, DB::table('criterios_acreditacion')->count());
    }

    public function test_las_migraciones_se_pueden_revertir(): void
    {
        $this->assertSame(0, Artisan::call('migrate:rollback', ['--force' => true]));
        $this->assertFalse(Schema::hasTable('actividades'));
        $this->assertFalse(Schema::hasColumn('users', 'rol'));
    }

    /** @return array{profesor:int, periodo:int, asignatura:int} */
    private function crearDatosBase(): array
    {
        $profesor = DB::table('users')->insertGetId([
            'name' => 'Ana Pérez', 'nombres' => 'Ana', 'apellidos' => 'Pérez',
            'email' => 'aperez@unal.edu.co', 'password' => bcrypt('secreto'),
        ]);
        $periodo = DB::table('periodos_academicos')->insertGetId([
            'codigo' => '2026-2', 'fecha_inicio' => '2026-08-01', 'fecha_fin' => '2026-12-15', 'activo' => true,
        ]);
        $programa = DB::table('programas_curriculares')->insertGetId([
            'codigo' => 'P-01', 'nombre' => 'Programa de prueba', 'nivel' => 'pregrado',
        ]);
        $asignatura = DB::table('asignaturas')->insertGetId([
            'codigo' => 'A-01', 'nombre' => 'Asignatura de prueba', 'creditos' => 3, 'programa_curricular_id' => $programa,
        ]);

        return compact('profesor', 'periodo', 'asignatura');
    }
}
