<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\User;
use Illuminate\Support\Collection;

class ConsolidadoDepartamentoService
{
    /**
     * Consolida toda la actividad académica y de extensión del departamento para un periodo.
     *
     * @return array<string, mixed>
     */
    public function obtenerConsolidadoPeriodo(PeriodoAcademico $periodo): array
    {
        // Todos los grupos del departamento en el periodo
        $grupos = Grupo::where('periodo_academico_id', $periodo->id)
            ->with(['asignatura.programaCurricular', 'profesor'])
            ->get();

        $grupoIds = $grupos->pluck('id')->toArray();

        // Todas las actividades desarrolladas en estos grupos
        $actividades = Actividad::where(function ($q) use ($grupoIds) {
            $q->whereIn('grupo_id', $grupoIds)
              ->orWhereHas('grupos', fn ($g) => $g->whereIn('grupos.id', $grupoIds));
        })
        ->with([
            'grupo.asignatura.programaCurricular',
            'grupo.profesor',
            'grupos.asignatura',
            'tipoActividad',
            'entidadExterna',
            'criterios',
            'evidencias',
        ])
        ->orderBy('fecha_inicio')
        ->get();

        // Profesores que tienen materias asignadas en este periodo
        $profesoresIds = $grupos->pluck('profesor_id')->unique()->filter()->values();
        $profesores = User::whereIn('id', $profesoresIds)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        // Totales globales
        $totales = [
            'docentes_activos' => $profesores->count(),
            'grupos_ofertados' => $grupos->count(),
            'asignaturas_distintas' => $grupos->pluck('asignatura_id')->unique()->count(),
            'actividades' => $actividades->count(),
            'registradas' => $actividades->where('estado', Actividad::ESTADO_REGISTRADA)->count(),
            'borrador' => $actividades->where('estado', Actividad::ESTADO_BORRADOR)->count(),
            'horas' => (float) $actividades->sum('duracion_horas'),
            'estudiantes' => (int) $actividades->sum('numero_estudiantes_participantes'),
            'evidencias' => (int) $actividades->sum(fn ($a) => $a->evidencias->count()),
        ];

        // Desglose por docente
        $porDocente = $profesores->map(function (User $docente) use ($grupos, $actividades) {
            $misGrupos = $grupos->where('profesor_id', $docente->id);
            $misGruposIds = $misGrupos->pluck('id')->toArray();

            $misActividades = $actividades->filter(function (Actividad $act) use ($misGruposIds) {
                return in_array($act->grupo_id, $misGruposIds)
                    || $act->grupos->pluck('id')->intersect($misGruposIds)->isNotEmpty();
            });

            return [
                'docente' => $docente,
                'nombre' => $docente->name,
                'documento' => $docente->documento_identidad ?? 'N/A',
                'dedicacion' => $docente->dedicacion ?? 'N/A',
                'categoria' => $docente->categoria ?? 'N/A',
                'grupos_count' => $misGrupos->count(),
                'materias_count' => $misGrupos->pluck('asignatura_id')->unique()->count(),
                'actividades_count' => $misActividades->count(),
                'registradas_count' => $misActividades->where('estado', Actividad::ESTADO_REGISTRADA)->count(),
                'horas_totales' => (float) $misActividades->sum('duracion_horas'),
                'estudiantes_impactados' => (int) $misActividades->sum('numero_estudiantes_participantes'),
                'evidencias_count' => (int) $misActividades->sum(fn ($a) => $a->evidencias->count()),
            ];
        });

        // Desglose por tipo de actividad a nivel de departamento
        $porTipo = $actividades->groupBy(fn ($a) => $a->tipoActividad?->nombre ?? 'Sin tipo')
            ->map(function (Collection $items, string $nombre) {
                return [
                    'nombre' => $nombre,
                    'categoria' => $items->first()->tipoActividad?->categoria ?? 'general',
                    'cantidad' => $items->count(),
                    'horas' => (float) $items->sum('duracion_horas'),
                    'estudiantes' => (int) $items->sum('numero_estudiantes_participantes'),
                ];
            })->values();

        // Desglose por programa curricular
        $porPrograma = $grupos->groupBy(fn ($g) => $g->asignatura?->programaCurricular?->nombre ?? 'Sin programa')
            ->map(function (Collection $items, string $programa) use ($actividades) {
                $grupoIdsPrograma = $items->pluck('id')->toArray();
                $actsPrograma = $actividades->filter(fn ($a) => in_array($a->grupo_id, $grupoIdsPrograma));

                return [
                    'programa' => $programa,
                    'grupos_count' => $items->count(),
                    'estudiantes_matriculados' => (int) $items->sum('numero_estudiantes'),
                    'actividades_count' => $actsPrograma->count(),
                    'horas_acumuladas' => (float) $actsPrograma->sum('duracion_horas'),
                ];
            })->values();

        // Criterios de acreditación tributados en el departamento
        $criterios = collect();
        foreach ($actividades as $act) {
            foreach ($act->criterios as $crit) {
                if (! $criterios->has($crit->id)) {
                    $criterios->put($crit->id, [
                        'criterio' => $crit,
                        'codigo' => $crit->codigo,
                        'nombre' => $crit->nombre,
                        'cantidad_actividades' => 0,
                    ]);
                }
                $item = $criterios->get($crit->id);
                $item['cantidad_actividades']++;
                $criterios->put($crit->id, $item);
            }
        }
        $criterios = $criterios->sortBy('codigo')->values();

        return [
            'periodo' => $periodo,
            'totales' => $totales,
            'por_docente' => $porDocente,
            'por_tipo' => $porTipo,
            'por_programa' => $porPrograma,
            'actividades' => $actividades,
            'criterios' => $criterios,
        ];
    }
}
