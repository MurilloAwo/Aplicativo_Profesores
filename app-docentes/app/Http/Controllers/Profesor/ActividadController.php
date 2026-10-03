<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profesor\StoreActividadRequest;
use App\Http\Requests\Profesor\UpdateActividadRequest;
use App\Models\Actividad;
use App\Models\CriterioAcreditacion;
use App\Models\EntidadExterna;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\TipoActividad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $periodos = PeriodoAcademico::orderByDesc('fecha_inicio')->get();
        $periodoActivo = PeriodoAcademico::actual();

        $periodoId = $request->filled('periodo_id')
            ? $request->input('periodo_id')
            : $periodoActivo?->id;

        $query = $user->esAdmin()
            ? Actividad::query()
            : Actividad::where(function ($q) use ($user) {
                $q->whereHas('grupo', fn ($g) => $g->where('profesor_id', $user->id))
                  ->orWhereHas('grupos', fn ($g) => $g->where('profesor_id', $user->id));
            });

        if ($periodoId) {
            $query->whereHas('grupo', fn ($g) => $g->where('periodo_academico_id', $periodoId));
        }

        if ($request->filled('grupo_id')) {
            $grupoId = $request->input('grupo_id');
            $query->where(function ($q) use ($grupoId) {
                $q->where('grupo_id', $grupoId)
                  ->orWhereHas('grupos', fn ($g) => $g->where('grupos.id', $grupoId));
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('tipo_actividad_id')) {
            $query->where('tipo_actividad_id', $request->input('tipo_actividad_id'));
        }

        if ($request->filled('buscar')) {
            $search = '%'.$request->input('buscar').'%';
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', $search)
                  ->orWhere('nombre_invitado', 'like', $search)
                  ->orWhere('descripcion', 'like', $search);
            });
        }

        $actividades = $query->with([
            'grupo.asignatura',
            'grupo.periodoAcademico',
            'tipoActividad',
            'entidadExterna',
            'evidencias',
            'criterios',
        ])
        ->orderByDesc('fecha_inicio')
        ->paginate(15)
        ->withQueryString();

        $misGrupos = $user->esAdmin()
            ? ($periodoId ? Grupo::where('periodo_academico_id', $periodoId)->with('asignatura')->get() : collect())
            : ($periodoId ? $user->grupos()->where('periodo_academico_id', $periodoId)->with('asignatura')->get() : $user->grupos()->with('asignatura')->get());

        $tipos = TipoActividad::orderBy('nombre')->get();

        return view('profesor.actividades.index', compact('actividades', 'periodos', 'periodoId', 'misGrupos', 'tipos'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $this->authorize('create', Actividad::class);

        $periodo = PeriodoAcademico::actual();
        if (! $periodo || ! $periodo->admiteRegistro()) {
            return redirect()->route('actividades.index')
                ->with('error', 'No hay un periodo académico activo que admita registro de actividades.');
        }

        $grupos = $user->esAdmin()
            ? Grupo::where('periodo_academico_id', $periodo->id)->with(['asignatura', 'profesor'])->get()
            : $user->grupos()->where('periodo_academico_id', $periodo->id)->with('asignatura')->get();

        if ($grupos->isEmpty()) {
            return redirect()->route('actividades.index')
                ->with('warning', 'No tiene materias asignadas en el periodo académico activo.');
        }

        $grupoSeleccionadoId = $request->input('grupo_id', $grupos->first()->id);
        $tipos = TipoActividad::where('activo', true)->orderBy('nombre')->get();
        $entidades = EntidadExterna::orderBy('nombre')->get();
        $criterios = CriterioAcreditacion::orderBy('codigo')->get();

        return view('profesor.actividades.create', compact('grupos', 'grupoSeleccionadoId', 'tipos', 'entidades', 'criterios', 'periodo'));
    }

    public function store(StoreActividadRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $actividad = DB::transaction(function () use ($validated, $request) {
            $actividad = Actividad::create([
                'grupo_id' => $validated['grupo_id'],
                'tipo_actividad_id' => $validated['tipo_actividad_id'],
                'entidad_externa_id' => $validated['entidad_externa_id'] ?? null,
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'],
                'objetivo' => $validated['objetivo'] ?? null,
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'] ?? null,
                'duracion_horas' => $validated['duracion_horas'],
                'lugar' => $validated['lugar'] ?? null,
                'modalidad' => $validated['modalidad'],
                'numero_estudiantes_participantes' => $validated['numero_estudiantes_participantes'],
                'nombre_invitado' => $validated['nombre_invitado'] ?? null,
                'cargo_invitado' => $validated['cargo_invitado'] ?? null,
                'resultados_obtenidos' => $validated['resultados_obtenidos'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => $validated['estado'],
            ]);

            // Sincronizar multigrupos (incluyendo el principal siempre)
            $todosGrupos = array_unique(array_merge(
                [(int) $actividad->grupo_id],
                array_map('intval', $request->input('grupos_adicionales', []))
            ));
            $actividad->grupos()->sync($todosGrupos);

            // Sincronizar criterios de acreditación
            $criterios = array_map('intval', $request->input('criterios', []));
            $actividad->criterios()->sync($criterios);

            return $actividad;
        });

        return redirect()->route('actividades.show', $actividad)
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function show(Actividad $actividad): View
    {
        $this->authorize('view', $actividad);

        $actividad->load([
            'grupo.asignatura.programaCurricular',
            'grupo.periodoAcademico',
            'grupo.profesor',
            'grupos.asignatura',
            'tipoActividad',
            'entidadExterna',
            'criterios',
            'evidencias',
        ]);

        return view('profesor.actividades.show', compact('actividad'));
    }

    public function edit(Actividad $actividad): View
    {
        $this->authorize('update', $actividad);

        $periodo = $actividad->grupo?->periodoAcademico;
        $user = auth()->user();

        $grupos = $user->esAdmin()
            ? Grupo::where('periodo_academico_id', $periodo?->id)->with(['asignatura', 'profesor'])->get()
            : $user->grupos()->where('periodo_academico_id', $periodo?->id)->with('asignatura')->get();

        $tipos = TipoActividad::where('activo', true)->orderBy('nombre')->get();
        $entidades = EntidadExterna::orderBy('nombre')->get();
        $criterios = CriterioAcreditacion::orderBy('codigo')->get();

        $gruposSeleccionados = $actividad->grupos->pluck('id')->toArray();
        $criteriosSeleccionados = $actividad->criterios->pluck('id')->toArray();

        return view('profesor.actividades.edit', compact(
            'actividad',
            'grupos',
            'tipos',
            'entidades',
            'criterios',
            'periodo',
            'gruposSeleccionados',
            'criteriosSeleccionados'
        ));
    }

    public function update(UpdateActividadRequest $request, Actividad $actividad): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($actividad, $validated, $request) {
            $actividad->update([
                'tipo_actividad_id' => $validated['tipo_actividad_id'],
                'entidad_externa_id' => $validated['entidad_externa_id'] ?? null,
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'],
                'objetivo' => $validated['objetivo'] ?? null,
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'] ?? null,
                'duracion_horas' => $validated['duracion_horas'],
                'lugar' => $validated['lugar'] ?? null,
                'modalidad' => $validated['modalidad'],
                'numero_estudiantes_participantes' => $validated['numero_estudiantes_participantes'],
                'nombre_invitado' => $validated['nombre_invitado'] ?? null,
                'cargo_invitado' => $validated['cargo_invitado'] ?? null,
                'resultados_obtenidos' => $validated['resultados_obtenidos'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => $validated['estado'],
            ]);

            // Sincronizar multigrupos (manteniendo el grupo principal)
            $todosGrupos = array_unique(array_merge(
                [(int) $actividad->grupo_id],
                array_map('intval', $request->input('grupos_adicionales', []))
            ));
            $actividad->grupos()->sync($todosGrupos);

            // Sincronizar criterios de acreditación
            $criterios = array_map('intval', $request->input('criterios', []));
            $actividad->criterios()->sync($criterios);
        });

        return redirect()->route('actividades.show', $actividad)
            ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad): RedirectResponse
    {
        $this->authorize('delete', $actividad);

        $actividad->delete();

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}
