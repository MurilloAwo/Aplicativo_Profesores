<?php

namespace Tests\Feature;

use App\Models\Actividad;
use App\Models\Asignatura;
use App\Models\CriterioAcreditacion;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\ProgramaCurricular;
use App\Models\TipoActividad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FlujoCompletoDocenteTest extends TestCase
{
    use RefreshDatabase;

    public function test_flujo_completo_docente_y_consolidado_departamental(): void
    {
        Storage::fake('local');

        // 1. Configuración de datos institucionales
        $periodo = PeriodoAcademico::factory()->create([
            'codigo' => '2026-1S',
            'activo' => true,
            'cerrado' => false,
            'fecha_inicio' => now()->startOfYear(),
            'fecha_fin' => now()->addMonths(5),
        ]);

        $programa = ProgramaCurricular::factory()->create([
            'nombre' => 'Ingeniería de Sistemas y Computación',
            'codigo' => '2542',
        ]);

        $asignatura = Asignatura::factory()->create([
            'programa_curricular_id' => $programa->id,
            'nombre' => 'Arquitectura de Software',
            'codigo' => 'AS-101',
            'creditos' => 3,
        ]);

        $profesor = User::factory()->create([
            'nombres' => 'Profesor',
            'apellidos' => 'Prueba UNAL',
            'email' => 'profesor.prueba@unal.edu.co',
            'rol' => User::ROL_PROFESOR,
            'activo' => true,
        ]);

        $grupo = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $profesor->id,
            'numero_grupo' => '1',
            'numero_estudiantes' => 30,
        ]);

        $tipoActividad = TipoActividad::factory()->create([
            'nombre' => 'Conferencia de Experto',
            'activo' => true,
        ]);

        $criterio = CriterioAcreditacion::factory()->create([
            'codigo' => 'ABET-SO-1',
            'nombre' => 'Resolución de Problemas Complejos',
        ]);

        $admin = User::factory()->create([
            'name' => 'Director de Departamento',
            'email' => 'director.computacion@unal.edu.co',
            'rol' => User::ROL_ADMIN,
            'activo' => true,
        ]);

        // 2. Profesor inicia sesión y consulta "Mis Materias"
        $responseMaterias = $this->actingAs($profesor)->get(route('materias.index'));
        $responseMaterias->assertOk();
        $responseMaterias->assertSee('Arquitectura de Software');
        $responseMaterias->assertSee('2026-1S');

        // 3. Profesor consulta el detalle de la materia
        $responseDetalle = $this->actingAs($profesor)->get(route('materias.show', $grupo));
        $responseDetalle->assertOk();
        $responseDetalle->assertSee('Arquitectura de Software');
        $responseDetalle->assertSee('Grupo 1');

        // 4. Profesor registra una actividad académica vinculada al grupo
        $responseCrearActividad = $this->actingAs($profesor)->post(route('actividades.store'), [
            'grupo_id' => $grupo->id,
            'tipo_actividad_id' => $tipoActividad->id,
            'titulo' => 'Charla sobre Microservicios con Arquitecto de Google',
            'descripcion' => 'Sesión magistral sobre arquitecturas distribuidas de alta escalabilidad.',
            'objetivo' => 'Fortalecer competencias de diseño arquitectónico en la nube.',
            'fecha_inicio' => now()->format('Y-m-d'),
            'duracion_horas' => 2.5,
            'modalidad' => 'virtual',
            'numero_estudiantes_participantes' => 28,
            'estado' => Actividad::ESTADO_REGISTRADA,
            'criterios' => [$criterio->id],
        ]);

        $this->assertDatabaseHas('actividades', [
            'grupo_id' => $grupo->id,
            'titulo' => 'Charla sobre Microservicios con Arquitecto de Google',
            'estado' => Actividad::ESTADO_REGISTRADA,
        ]);

        $actividad = Actividad::where('titulo', 'Charla sobre Microservicios con Arquitecto de Google')->first();
        $this->assertNotNull($actividad);
        $responseCrearActividad->assertRedirect(route('actividades.show', $actividad));

        // 5. Profesor adjunta evidencia digital a la actividad
        $archivoPrueba = UploadedFile::fake()->create('asistencia_y_acta.pdf', 500, 'application/pdf');
        $responseEvidencia = $this->actingAs($profesor)->post(route('actividades.evidencias.store', $actividad), [
            'tipo' => 'lista_asistencia',
            'archivo' => $archivoPrueba,
        ]);

        $responseEvidencia->assertRedirect();
        $this->assertDatabaseHas('evidencias', [
            'actividad_id' => $actividad->id,
            'tipo' => 'lista_asistencia',
            'nombre_original' => 'asistencia_y_acta.pdf',
        ]);

        $evidencia = $actividad->evidencias()->first();
        $this->assertNotNull($evidencia);
        Storage::disk('local')->assertExists($evidencia->ruta);

        // 6. Profesor descarga la evidencia de forma segura
        $responseDescarga = $this->actingAs($profesor)->get(route('evidencias.download', $evidencia));
        $responseDescarga->assertOk();

        // 7. Profesor visualiza el resumen semestral en pantalla
        $responseResumen = $this->actingAs($profesor)->get(route('resumen.index'));
        $responseResumen->assertOk();
        $responseResumen->assertSee('Charla sobre Microservicios con Arquitecto de Google');
        $responseResumen->assertSee('Arquitectura de Software');

        // 8. Profesor descarga el resumen semestral en PDF
        $responsePdfDocente = $this->actingAs($profesor)->get(route('resumen.pdf'));
        $responsePdfDocente->assertOk();
        $responsePdfDocente->assertHeader('content-type', 'application/pdf');

        // 9. Profesor descarga el resumen semestral en Excel
        $responseExcelDocente = $this->actingAs($profesor)->get(route('resumen.excel'));
        $responseExcelDocente->assertOk();
        $this->assertTrue(str_contains(
            $responseExcelDocente->headers->get('content-disposition') ?? '',
            '.xlsx'
        ));

        // 10. Administrador consulta consolidado departamental en pantalla
        $responseConsolidado = $this->actingAs($admin)->get(route('admin.consolidado.index'));
        $responseConsolidado->assertOk();
        $responseConsolidado->assertSee('Profesor Prueba UNAL');
        $responseConsolidado->assertSee('Consolidado');

        // 11. Administrador descarga consolidado departamental en PDF
        $responsePdfAdmin = $this->actingAs($admin)->get(route('admin.consolidado.pdf'));
        $responsePdfAdmin->assertOk();
        $responsePdfAdmin->assertHeader('content-type', 'application/pdf');

        // 12. Administrador descarga consolidado departamental en Excel
        $responseExcelAdmin = $this->actingAs($admin)->get(route('admin.consolidado.excel'));
        $responseExcelAdmin->assertOk();
        $this->assertTrue(str_contains(
            $responseExcelAdmin->headers->get('content-disposition') ?? '',
            '.xlsx'
        ));
    }
}
