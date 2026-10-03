<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Periodo activo o el seleccionado por el docente
        $periodos = PeriodoAcademico::orderBy('codigo', 'desc')->get();
        $periodoActivo = PeriodoAcademico::actual();

        $periodoId = $request->input('periodo_id', $periodoActivo?->id ?? $periodos->first()?->id);
        $periodoSeleccionado = $periodos->firstWhere('id', $periodoId);

        $grupos = Grupo::where('profesor_id', $user->id)
            ->when($periodoId, fn ($q) => $q->where('periodo_academico_id', $periodoId))
            ->with(['asignatura.programaCurricular', 'periodoAcademico'])
            ->withCount(['actividadesPrincipales', 'actividades'])
            ->orderBy('numero_grupo')
            ->get();

        $totalEstudiantes = $grupos->sum('numero_estudiantes');
        $totalActividades = $grupos->sum('actividades_principales_count');

        return view('profesor.materias.index', compact(
            'grupos',
            'periodos',
            'periodoActivo',
            'periodoSeleccionado',
            'totalEstudiantes',
            'totalActividades'
        ));
    }

    public function show(Grupo $grupo): View
    {
        $this->authorize('view', $grupo);

        $grupo->load(['asignatura.programaCurricular', 'periodoAcademico', 'profesor']);

        $actividades = $grupo->actividadesPrincipales()
            ->with(['tipoActividad', 'entidadExterna', 'evidencias', 'criterios'])
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(10);

        $totalHoras = $grupo->actividadesPrincipales()->sum('duracion_horas');
        $estudiantesImpactados = $grupo->actividadesPrincipales()->sum('numero_estudiantes_participantes');

        return view('profesor.materias.show', compact(
            'grupo',
            'actividades',
            'totalHoras',
            'estudiantesImpactados'
        ));
    }
}
