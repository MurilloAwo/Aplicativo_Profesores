<?php

namespace Tests\Feature;

use App\Models\Actividad;
use App\Models\Asignatura;
use App\Models\CriterioAcreditacion;
use App\Models\EntidadExterna;
use App\Models\Evidencia;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\TipoActividad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActividadesEvidenciasTest extends TestCase
{
    use RefreshDatabase;

    private User $profesor;
    private User $otroProfesor;
    private User $admin;
    private PeriodoAcademico $periodoActivo;
    private PeriodoAcademico $periodoCerrado;
    private TipoActividad $tipo;
    private Grupo $grupoProfesor;
    private Grupo $grupo2Profesor;
    private Grupo $grupoOtroProfesor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->profesor = User::factory()->profesor()->create();
        $this->otroProfesor = User::factory()->profesor()->create();

        $this->periodoActivo = PeriodoAcademico::factory()->activo()->create([
            'codigo' => '2026-1',
            'cerrado' => false,
        ]);

        $this->periodoCerrado = PeriodoAcademico::factory()->cerrado()->create([
            'codigo' => '2025-2',
            'activo' => false,
            'cerrado' => true,
        ]);

        $this->tipo = TipoActividad::factory()->create([
            'nombre' => 'Charla técnica',
            'categoria' => 'extension',
            'activo' => true,
            'requiere_entidad_externa' => false,
        ]);

        $asignatura = Asignatura::factory()->create();

        $this->grupoProfesor = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $this->periodoActivo->id,
            'profesor_id' => $this->profesor->id,
            'numero_grupo' => 1,
        ]);

        $this->grupo2Profesor = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $this->periodoActivo->id,
            'profesor_id' => $this->profesor->id,
            'numero_grupo' => 2,
        ]);

        $this->grupoOtroProfesor = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $this->periodoActivo->id,
            'profesor_id' => $this->otroProfesor->id,
            'numero_grupo' => 3,
        ]);
    }

    public function test_profesor_puede_listar_sus_actividades_y_no_las_de_otros(): void
    {
        $actividadPropia = Actividad::factory()->create([
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Actividad de prueba propia',
        ]);

        $actividadAjena = Actividad::factory()->create([
            'grupo_id' => $this->grupoOtroProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Actividad de otro docente',
        ]);

        $response = $this->actingAs($this->profesor)->get(route('actividades.index'));

        $response->assertOk();
        $response->assertSee('Actividad de prueba propia');
        $response->assertDontSee('Actividad de otro docente');
    }

    public function test_profesor_puede_crear_actividad_con_multigrupo_y_criterios(): void
    {
        $criterio1 = CriterioAcreditacion::factory()->create(['codigo' => 'CRIT-01']);
        $criterio2 = CriterioAcreditacion::factory()->create(['codigo' => 'CRIT-02']);

        $payload = [
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Taller de Clean Architecture',
            'descripcion' => 'Sesión intensiva sobre patrones arquitectónicos',
            'objetivo' => 'Aprender diseño por capas',
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
            'duracion_horas' => 4.0,
            'modalidad' => 'presencial',
            'lugar' => 'Aula 401',
            'numero_estudiantes_participantes' => 30,
            'nombre_invitado' => 'Ing. Juan Pérez',
            'cargo_invitado' => 'Tech Lead',
            'resultados_obtenidos' => 'Estudiantes implementaron casos de uso',
            'observaciones' => 'Buena participación',
            'estado' => 'registrada',
            'grupos_adicionales' => [$this->grupo2Profesor->id],
            'criterios' => [$criterio1->id, $criterio2->id],
        ];

        $response = $this->actingAs($this->profesor)->post(route('actividades.store'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('actividades', [
            'titulo' => 'Taller de Clean Architecture',
            'grupo_id' => $this->grupoProfesor->id,
            'estado' => 'registrada',
        ]);

        $actividad = Actividad::where('titulo', 'Taller de Clean Architecture')->first();
        $this->assertNotNull($actividad);

        // El pivote multigrupo debe contener tanto el grupo principal como el adicional
        $this->assertCount(2, $actividad->grupos);
        $this->assertTrue($actividad->grupos->contains($this->grupoProfesor));
        $this->assertTrue($actividad->grupos->contains($this->grupo2Profesor));

        // Los criterios deben estar asociados
        $this->assertCount(2, $actividad->criterios);
        $this->assertTrue($actividad->criterios->contains($criterio1));
        $this->assertTrue($actividad->criterios->contains($criterio2));
    }

    public function test_profesor_puede_actualizar_su_actividad(): void
    {
        $actividad = Actividad::factory()->create([
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Título Inicial',
            'descripcion' => 'Descripción Inicial',
            'estado' => 'borrador',
        ]);

        $payload = [
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Título Actualizado',
            'descripcion' => 'Descripción Actualizada',
            'fecha_inicio' => now()->format('Y-m-d'),
            'duracion_horas' => 3.5,
            'modalidad' => 'virtual',
            'numero_estudiantes_participantes' => 20,
            'estado' => 'registrada',
        ];

        $response = $this->actingAs($this->profesor)->put(route('actividades.update', $actividad), $payload);

        $response->assertRedirect(route('actividades.show', $actividad));

        $this->assertDatabaseHas('actividades', [
            'id' => $actividad->id,
            'titulo' => 'Título Actualizado',
            'modalidad' => 'virtual',
            'estado' => 'registrada',
        ]);
    }

    public function test_profesor_puede_eliminar_su_actividad_soft_delete(): void
    {
        $actividad = Actividad::factory()->create([
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
        ]);

        $response = $this->actingAs($this->profesor)->delete(route('actividades.destroy', $actividad));

        $response->assertRedirect(route('actividades.index'));
        $this->assertSoftDeleted('actividades', ['id' => $actividad->id]);
    }

    public function test_profesor_recibe_403_al_intentar_acceder_o_modificar_actividad_ajena(): void
    {
        $actividadAjena = Actividad::factory()->create([
            'grupo_id' => $this->grupoOtroProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
        ]);

        // Ver detalle ajeno
        $resShow = $this->actingAs($this->profesor)->get(route('actividades.show', $actividadAjena));
        $resShow->assertForbidden();

        // Editar ajeno
        $resEdit = $this->actingAs($this->profesor)->get(route('actividades.edit', $actividadAjena));
        $resEdit->assertForbidden();

        // Actualizar ajeno
        $resUpdate = $this->actingAs($this->profesor)->put(route('actividades.update', $actividadAjena), [
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Hack intento',
            'descripcion' => 'Intento no autorizado',
            'fecha_inicio' => now()->format('Y-m-d'),
            'duracion_horas' => 1,
            'modalidad' => 'presencial',
            'numero_estudiantes_participantes' => 10,
            'estado' => 'borrador',
        ]);
        $resUpdate->assertForbidden();

        // Eliminar ajeno
        $resDelete = $this->actingAs($this->profesor)->delete(route('actividades.destroy', $actividadAjena));
        $resDelete->assertForbidden();
    }

    public function test_no_se_pueden_crear_ni_modificar_actividades_en_periodo_cerrado(): void
    {
        $asignatura = Asignatura::factory()->create();
        $grupoCerrado = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $this->periodoCerrado->id,
            'profesor_id' => $this->profesor->id,
            'numero_grupo' => 1,
        ]);

        // Intentar registrar en grupo de periodo cerrado
        $resCreate = $this->actingAs($this->profesor)->post(route('actividades.store'), [
            'grupo_id' => $grupoCerrado->id,
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Actividad extemporánea',
            'descripcion' => 'Intento en periodo cerrado',
            'fecha_inicio' => now()->format('Y-m-d'),
            'duracion_horas' => 2,
            'modalidad' => 'presencial',
            'numero_estudiantes_participantes' => 15,
            'estado' => 'registrada',
        ]);
        $resCreate->assertForbidden();

        // Intentar actualizar actividad existente en periodo cerrado
        $actividadCerrada = Actividad::factory()->create([
            'grupo_id' => $grupoCerrado->id,
            'tipo_actividad_id' => $this->tipo->id,
        ]);

        $resUpdate = $this->actingAs($this->profesor)->put(route('actividades.update', $actividadCerrada), [
            'tipo_actividad_id' => $this->tipo->id,
            'titulo' => 'Modificación en cerrado',
            'descripcion' => 'No debe permitirse',
            'fecha_inicio' => now()->format('Y-m-d'),
            'duracion_horas' => 2,
            'modalidad' => 'presencial',
            'numero_estudiantes_participantes' => 15,
            'estado' => 'registrada',
        ]);
        $resUpdate->assertForbidden();
    }

    public function test_profesor_puede_subir_y_descargar_evidencias_en_disco_local_privado(): void
    {
        Storage::fake('local');

        $actividad = Actividad::factory()->create([
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
        ]);

        $archivo = UploadedFile::fake()->create('lista_asistencia_estudiantes.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->profesor)->post(
            route('actividades.evidencias.store', $actividad),
            [
                'tipo' => 'lista_asistencia',
                'archivo' => $archivo,
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('evidencias', [
            'actividad_id' => $actividad->id,
            'tipo' => 'lista_asistencia',
            'nombre_original' => 'lista_asistencia_estudiantes.pdf',
        ]);

        $evidencia = Evidencia::where('actividad_id', $actividad->id)->first();
        $this->assertNotNull($evidencia);

        // El archivo se almacena en el disco local privado
        Storage::disk('local')->assertExists($evidencia->ruta);

        // Descarga autorizada
        $resDescarga = $this->actingAs($this->profesor)->get(route('evidencias.download', $evidencia));
        $resDescarga->assertOk();
        $resDescarga->assertDownload('lista_asistencia_estudiantes.pdf');

        // Visualización inline
        $resInline = $this->actingAs($this->profesor)->get(route('evidencias.download', [$evidencia, 'inline' => 1]));
        $resInline->assertOk();
    }

    public function test_validacion_de_evidencias_rechaza_archivos_mayores_a_10mb_o_extensiones_invalidas(): void
    {
        Storage::fake('local');

        $actividad = Actividad::factory()->create([
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
        ]);

        // Archivo que excede 10 MB (10240 KB) -> 11 MB = 11264 KB
        $archivoPesado = UploadedFile::fake()->create('video_pesado.mp4', 11500, 'video/mp4');

        $resPesado = $this->actingAs($this->profesor)->post(
            route('actividades.evidencias.store', $actividad),
            [
                'tipo' => 'otro',
                'archivo' => $archivoPesado,
            ]
        );
        $resPesado->assertSessionHasErrors(['archivo']);

        // Archivo ejecutable no permitido
        $archivoInseguro = UploadedFile::fake()->create('script.exe', 50, 'application/x-msdownload');

        $resInseguro = $this->actingAs($this->profesor)->post(
            route('actividades.evidencias.store', $actividad),
            [
                'tipo' => 'otro',
                'archivo' => $archivoInseguro,
            ]
        );
        $resInseguro->assertSessionHasErrors(['archivo']);
    }

    public function test_profesor_ajeno_no_puede_descargar_ni_eliminar_evidencia(): void
    {
        Storage::fake('local');

        $actividad = Actividad::factory()->create([
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
        ]);

        $archivo = UploadedFile::fake()->create('certificado.pdf', 100, 'application/pdf');
        $this->actingAs($this->profesor)->post(route('actividades.evidencias.store', $actividad), [
            'tipo' => 'certificado',
            'archivo' => $archivo,
        ]);

        $evidencia = Evidencia::where('actividad_id', $actividad->id)->first();

        // Otro profesor intenta descargar
        $resDescarga = $this->actingAs($this->otroProfesor)->get(route('evidencias.download', $evidencia));
        $resDescarga->assertForbidden();

        // Otro profesor intenta eliminar
        $resEliminar = $this->actingAs($this->otroProfesor)->delete(route('evidencias.destroy', $evidencia));
        $resEliminar->assertForbidden();

        // Administrador tiene bypass y puede descargar
        $resAdmin = $this->actingAs($this->admin)->get(route('evidencias.download', $evidencia));
        $resAdmin->assertOk();
    }

    public function test_profesor_puede_eliminar_evidencia_propia(): void
    {
        Storage::fake('local');

        $actividad = Actividad::factory()->create([
            'grupo_id' => $this->grupoProfesor->id,
            'tipo_actividad_id' => $this->tipo->id,
        ]);

        $archivo = UploadedFile::fake()->create('acta.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $this->actingAs($this->profesor)->post(route('actividades.evidencias.store', $actividad), [
            'tipo' => 'acta',
            'archivo' => $archivo,
        ]);

        $evidencia = Evidencia::where('actividad_id', $actividad->id)->first();
        $ruta = $evidencia->ruta;
        Storage::disk('local')->assertExists($ruta);

        $response = $this->actingAs($this->profesor)->delete(route('evidencias.destroy', $evidencia));
        $response->assertRedirect();

        $this->assertDatabaseMissing('evidencias', ['id' => $evidencia->id]);
        Storage::disk('local')->assertMissing($ruta);
    }
}
