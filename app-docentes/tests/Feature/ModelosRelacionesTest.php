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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelosRelacionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_actualiza_name_automaticamente_y_roles(): void
    {
        $user = new User([
            'nombres' => 'Carlos',
            'apellidos' => 'Ramírez',
            'documento' => '10203040',
            'email' => 'cramirez@unal.edu.co',
            'password' => 'password123',
            'tipo_vinculacion' => 'planta',
            'dedicacion' => 'Exclusiva',
            'categoria' => 'Titular',
        ]);
        $user->save();

        $this->assertSame('Carlos Ramírez', $user->name);
        $this->assertTrue($user->esProfesor());
        $this->assertFalse($user->esAdmin());
        $this->assertTrue($user->activo);

        $admin = new User([
            'nombres' => 'Admin',
            'apellidos' => 'Sistema',
            'email' => 'admin@unal.edu.co',
            'password' => 'password123',
        ]);
        $admin->rol = User::ROL_ADMIN;
        $admin->save();

        $this->assertTrue($admin->esAdmin());
        $this->assertFalse($admin->esProfesor());
    }

    public function test_relaciones_completas_del_modelo_academico(): void
    {
        $programa = ProgramaCurricular::create([
            'codigo' => '2541',
            'nombre' => 'Ingeniería de Sistemas y Computación',
            'nivel' => 'pregrado',
        ]);

        $asignatura = Asignatura::create([
            'codigo' => '2016699',
            'nombre' => 'Ingeniería de Software II',
            'creditos' => 3,
            'tipologia' => 'Disciplinar Obligatoria',
            'programa_curricular_id' => $programa->id,
        ]);

        $periodo = PeriodoAcademico::create([
            'codigo' => '2026-2',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-12-15',
            'activo' => true,
            'cerrado' => false,
        ]);

        $profesor = new User([
            'nombres' => 'Laura',
            'apellidos' => 'Gómez',
            'documento' => '98765432',
            'email' => 'lgomez@unal.edu.co',
            'password' => 'secret123',
        ]);
        $profesor->save();

        $grupo = Grupo::create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $profesor->id,
            'numero_grupo' => '1',
            'modalidad' => 'presencial',
            'horario' => 'Mié-Vie 07:00-09:00',
            'numero_estudiantes' => 35,
        ]);

        $tipo = TipoActividad::create([
            'nombre' => 'Visita Empresarial',
            'descripcion' => 'Visita técnica a instalaciones empresariales',
            'categoria' => 'relacion_sector_externo',
            'requiere_entidad_externa' => true,
            'activo' => true,
        ]);

        $entidad = EntidadExterna::create([
            'nombre' => 'Tech Corp Colombia',
            'nit' => '900123456-1',
            'sector' => 'Tecnología',
            'ciudad' => 'Bogotá',
            'contacto' => 'contacto@techcorp.com',
        ]);

        $actividad = Actividad::create([
            'grupo_id' => $grupo->id,
            'tipo_actividad_id' => $tipo->id,
            'entidad_externa_id' => $entidad->id,
            'titulo' => 'Visita a Centro de Datos',
            'descripcion' => 'Recorrido técnico y charla sobre arquitectura cloud',
            'objetivo' => 'Conocer infraestructura de alta disponibilidad',
            'fecha_inicio' => '2026-09-15',
            'fecha_fin' => '2026-09-15',
            'duracion_horas' => 4.5,
            'lugar' => 'Sede Empresarial',
            'modalidad' => 'presencial',
            'numero_estudiantes_participantes' => 30,
            'nombre_invitado' => 'Ing. Juan Pérez',
            'cargo_invitado' => 'Líder DevOps',
            'resultados_obtenidos' => 'Estudiantes comprendieron escalabilidad física y virtual',
            'observaciones' => 'Actividad realizada sin contratiempos',
            'estado' => Actividad::ESTADO_REGISTRADA,
        ]);

        // Vinculación multigrupo
        $actividad->grupos()->attach($grupo->id);

        $criterio = CriterioAcreditacion::create([
            'codigo' => 'FAC-03',
            'nombre' => 'Interacción con el entorno',
            'descripcion' => 'Factor de impacto y vinculación con la industria',
        ]);
        $actividad->criterios()->attach($criterio->id);

        $evidencia = Evidencia::create([
            'actividad_id' => $actividad->id,
            'tipo' => 'lista_asistencia',
            'nombre_original' => 'asistencia_visita.pdf',
            'ruta' => 'evidencias/2026/asistencia_visita.pdf',
            'mime' => 'application/pdf',
            'tamano' => 204800,
        ]);

        // Verificaciones de relaciones
        $this->assertTrue($programa->asignaturas->contains($asignatura));
        $this->assertSame($programa->id, $asignatura->programaCurricular->id);

        $this->assertTrue($asignatura->grupos->contains($grupo));
        $this->assertSame($asignatura->id, $grupo->asignatura->id);
        $this->assertSame($periodo->id, $grupo->periodoAcademico->id);
        $this->assertSame($profesor->id, $grupo->profesor->id);
        $this->assertTrue($profesor->grupos->contains($grupo));

        $this->assertSame($grupo->id, $actividad->grupo->id);
        $this->assertTrue($actividad->grupos->contains($grupo));
        $this->assertTrue($grupo->actividades->contains($actividad));
        $this->assertTrue($grupo->actividadesPrincipales->contains($actividad));

        $this->assertSame($tipo->id, $actividad->tipoActividad->id);
        $this->assertSame($entidad->id, $actividad->entidadExterna->id);

        $this->assertTrue($actividad->criterios->contains($criterio));
        $this->assertTrue($criterio->actividades->contains($actividad));

        $this->assertTrue($actividad->evidencias->contains($evidencia));
        $this->assertSame($actividad->id, $evidencia->actividad->id);

        // HasManyThrough User -> Actividades
        $this->assertTrue($profesor->actividades->contains($actividad));

        // Scopes
        $this->assertTrue(PeriodoAcademico::activo()->get()->contains($periodo));
        $this->assertSame($periodo->id, PeriodoAcademico::actual()?->id);
        $this->assertTrue($periodo->admiteRegistro());

        $this->assertTrue(Actividad::delProfesor($profesor)->get()->contains($actividad));
        $this->assertTrue(Actividad::delPeriodo($periodo)->get()->contains($actividad));

        // Soft deletes
        $actividad->delete();
        $this->assertSoftDeleted('actividades', ['id' => $actividad->id]);
        $this->assertCount(0, Actividad::all());
        $this->assertCount(1, Actividad::withTrashed()->get());
    }

    public function test_periodo_cerrado_no_admite_registro(): void
    {
        $periodo = PeriodoAcademico::create([
            'codigo' => '2026-1',
            'fecha_inicio' => '2026-02-01',
            'fecha_fin' => '2026-06-30',
            'activo' => false,
            'cerrado' => true,
        ]);

        $this->assertFalse($periodo->admiteRegistro());

        $periodoActivoCerrado = PeriodoAcademico::create([
            'codigo' => '2026-3',
            'fecha_inicio' => '2026-07-01',
            'fecha_fin' => '2026-07-31',
            'activo' => true,
            'cerrado' => true,
        ]);

        $this->assertFalse($periodoActivoCerrado->admiteRegistro());
    }
}
