<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GrupoController extends Controller
{
    public function index(Request $request): View
    {
        $periodos = PeriodoAcademico::orderBy('codigo', 'desc')->get();
        $periodoSeleccionadoId = $request->input('periodo_id', PeriodoAcademico::actual()?->id ?? $periodos->first()?->id);

        $query = Grupo::with(['asignatura.programaCurricular', 'profesor', 'periodoAcademico'])
            ->withCount('actividadesPrincipales');

        if ($periodoSeleccionadoId) {
            $query->where('periodo_academico_id', $periodoSeleccionadoId);
        }

        if ($request->filled('profesor_id')) {
            $query->where('profesor_id', $request->input('profesor_id'));
        }

        $grupos = $query->orderBy('asignatura_id')->orderBy('numero_grupo')->paginate(15)->withQueryString();
        $profesores = User::where('rol', User::ROL_PROFESOR)->where('activo', true)->orderBy('apellidos')->get();

        return view('admin.grupos.index', compact('grupos', 'periodos', 'periodoSeleccionadoId', 'profesores'));
    }

    public function create(): View
    {
        $asignaturas = Asignatura::orderBy('nombre')->get();
        $periodos = PeriodoAcademico::orderBy('codigo', 'desc')->get();
        $profesores = User::where('rol', User::ROL_PROFESOR)->where('activo', true)->orderBy('apellidos')->get();

        return view('admin.grupos.create', compact('asignaturas', 'periodos', 'profesores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asignatura_id' => ['required', 'exists:asignaturas,id'],
            'periodo_academico_id' => ['required', 'exists:periodos_academicos,id'],
            'profesor_id' => ['required', 'exists:users,id'],
            'numero_grupo' => [
                'required', 'string', 'max:10',
                Rule::unique('grupos')->where(fn ($q) => $q->where('asignatura_id', $request->asignatura_id)
                    ->where('periodo_academico_id', $request->periodo_academico_id)),
            ],
            'modalidad' => ['required', Rule::in(['presencial', 'virtual', 'hibrida'])],
            'horario' => ['nullable', 'string', 'max:255'],
            'numero_estudiantes' => ['required', 'integer', 'min:0', 'max:500'],
        ]);

        Grupo::create($validated);

        return redirect()->route('admin.grupos.index', ['periodo_id' => $validated['periodo_academico_id']])
            ->with('success', 'Grupo creado exitosamente.');
    }

    public function edit(Grupo $grupo): View
    {
        $asignaturas = Asignatura::orderBy('nombre')->get();
        $periodos = PeriodoAcademico::orderBy('codigo', 'desc')->get();
        $profesores = User::where('rol', User::ROL_PROFESOR)->orderBy('apellidos')->get();

        return view('admin.grupos.edit', compact('grupo', 'asignaturas', 'periodos', 'profesores'));
    }

    public function update(Request $request, Grupo $grupo): RedirectResponse
    {
        $validated = $request->validate([
            'asignatura_id' => ['required', 'exists:asignaturas,id'],
            'periodo_academico_id' => ['required', 'exists:periodos_academicos,id'],
            'profesor_id' => ['required', 'exists:users,id'],
            'numero_grupo' => [
                'required', 'string', 'max:10',
                Rule::unique('grupos')->ignore($grupo->id)
                    ->where(fn ($q) => $q->where('asignatura_id', $request->asignatura_id)
                        ->where('periodo_academico_id', $request->periodo_academico_id)),
            ],
            'modalidad' => ['required', Rule::in(['presencial', 'virtual', 'hibrida'])],
            'horario' => ['nullable', 'string', 'max:255'],
            'numero_estudiantes' => ['required', 'integer', 'min:0', 'max:500'],
        ]);

        $grupo->update($validated);

        return redirect()->route('admin.grupos.index', ['periodo_id' => $validated['periodo_academico_id']])
            ->with('success', 'Grupo actualizado.');
    }

    public function destroy(Grupo $grupo): RedirectResponse
    {
        if ($grupo->actividadesPrincipales()->exists() || $grupo->actividades()->exists()) {
            return back()->with('error', 'No se puede eliminar el grupo porque tiene actividades registradas.');
        }

        $periodoId = $grupo->periodo_academico_id;
        $grupo->delete();

        return redirect()->route('admin.grupos.index', ['periodo_id' => $periodoId])
            ->with('success', 'Grupo eliminado.');
    }
}
