<?php

namespace Tests\Feature;

use App\Models\Actividad;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\ProgramaCurricular;
use App\Models\TipoActividad;
use App\Models\User;
use App\Rules\SafeEmailRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class EndurecimientoYSeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_safe_email_rule_detects_and_blocks_crlf_injection(): void
    {
        $rule = new SafeEmailRule();

        $v1 = Validator::make(['email' => "evil@example.com\r\nBcc: victim@example.com"], ['email' => ['required', $rule]]);
        $this->assertTrue($v1->fails());
        $this->assertEquals('El correo electrónico contiene caracteres no permitidos (CRLF).', $v1->errors()->first('email'));

        $v2 = Validator::make(['email' => "evil@example.com\nSubject: spam"], ['email' => ['required', $rule]]);
        $this->assertTrue($v2->fails());
        $this->assertEquals('El correo electrónico contiene caracteres no permitidos (CRLF).', $v2->errors()->first('email'));

        $v3 = Validator::make(['email' => 'valid.docente@unal.edu.co'], ['email' => ['required', $rule]]);
        $this->assertTrue($v3->passes());
    }

    public function test_profesor_cannot_access_admin_routes_and_receives_403(): void
    {
        $profesor = User::factory()->create(['rol' => User::ROL_PROFESOR]);

        $response = $this->actingAs($profesor)->get(route('admin.usuarios.index'));
        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login_when_accessing_protected_routes(): void
    {
        $response = $this->get(route('materias.index'));
        $response->assertRedirect(route('login'));

        $responseAdmin = $this->get(route('admin.usuarios.index'));
        $responseAdmin->assertRedirect(route('login'));
    }

    public function test_profesor_cannot_edit_or_delete_another_profesors_activity(): void
    {
        $periodo = PeriodoAcademico::factory()->create(['activo' => true, 'cerrado' => false]);
        $programa = ProgramaCurricular::factory()->create();
        $asignatura = Asignatura::factory()->create(['programa_curricular_id' => $programa->id]);

        $profesor1 = User::factory()->create(['rol' => User::ROL_PROFESOR]);
        $profesor2 = User::factory()->create(['rol' => User::ROL_PROFESOR]);

        $grupo1 = Grupo::factory()->create([
            'asignatura_id' => $asignatura->id,
            'periodo_academico_id' => $periodo->id,
            'profesor_id' => $profesor1->id,
        ]);

        $actividad = Actividad::factory()->create([
            'grupo_id' => $grupo1->id,
            'titulo' => 'Actividad de Profesor 1',
        ]);

        // Profesor 2 intenta ver la actividad de Profesor 1
        $responseShow = $this->actingAs($profesor2)->get(route('actividades.show', $actividad));
        $responseShow->assertStatus(403);

        // Profesor 2 intenta editar la actividad de Profesor 1
        $responseEdit = $this->actingAs($profesor2)->get(route('actividades.edit', $actividad));
        $responseEdit->assertStatus(403);

        // Profesor 2 intenta actualizar la actividad de Profesor 1
        $responseUpdate = $this->actingAs($profesor2)->put(route('actividades.update', $actividad), [
            'titulo' => 'Título Modificado por Tercero',
        ]);
        $responseUpdate->assertStatus(403);

        // Profesor 2 intenta eliminar la actividad de Profesor 1
        $responseDestroy = $this->actingAs($profesor2)->delete(route('actividades.destroy', $actividad));
        $responseDestroy->assertStatus(403);
    }

    public function test_custom_404_page_renders_with_institutional_branding(): void
    {
        $profesor = User::factory()->create(['rol' => User::ROL_PROFESOR]);

        $response = $this->actingAs($profesor)->get('/ruta-inexistente-unal-12345');
        $response->assertStatus(404);
        $response->assertSee('Error 404');
        $response->assertSee('Página no encontrada');
        $response->assertSee('Universidad Nacional de Colombia');
    }

    public function test_admin_form_request_validates_required_fields_and_types(): void
    {
        $admin = User::factory()->create(['rol' => User::ROL_ADMIN]);

        // Intentar crear asignatura con créditos negativos y código vacío
        $response = $this->actingAs($admin)->post(route('admin.asignaturas.store'), [
            'codigo' => '',
            'nombre' => '',
            'creditos' => -5,
        ]);

        $response->assertSessionHasErrors(['codigo', 'nombre', 'creditos', 'programa_curricular_id']);
    }
}
