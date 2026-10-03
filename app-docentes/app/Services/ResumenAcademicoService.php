<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\User;
use Illuminate\Support\Collection;

class ResumenAcademicoService
{
    /**
     * Genera el consolidado semestral de actividades, horas y evidencias para un docente y periodo.
     *
     * @return array<string, mixed>
     */
    public function obtenerResumenProfesor(User $profesor, PeriodoAcademico $periodo): array
    {
        // Grupos asignados al profesor en este periodo
        $grupos = Grupo::where('profesor_id', $profesor->id)
            ->where('periodo_academico_id', $periodo->id)
            ->with(['asignatura.programaCurricular'])
            ->orderBy('numero_grupo')
            ->get();

        $grupoIds = $grupos->pluck('id')->toArray();

        // Actividades vinculadas a sus grupos en este periodo
        $actividades = Actividad::where(function ($q) use ($grupoIds) {
            $q->whereIn('grupo_id', $grupoIds)
              ->orWhereHas('grupos', fn ($g) => $g->whereIn('grupos.id', $grupoIds));
        })
        ->with([
            'grupo.asignatura.programaCurricular',
            'grupos.asignatura',
            'tipoActividad',
            'entidadExterna',
            'criterios',
            'evidencias',
        ])
        ->orderBy('fecha_inicio')
        ->get();

        // Totales cuantitativos
        $totalMaterias = $grupos->pluck('asignatura_id')->unique()->count();
        $totalGrupos = $grupos->count();
        $totalActividades = $actividades->count();
        $totalRegistradas = $actividades->where('estado', Actividad::ESTADO_REGISTRADA)->count();
        $totalBorrador = $actividades->where('estado', Actividad::ESTADO_BORRADOR)->count();
        $totalHoras = (float) $actividades->sum('duracion_horas');
        $totalEstudiantes = (int) $actividades->sum('numero_estudiantes_participantes');
        $totalEvidencias = (int) $actividades->sum(fn ($a) => $a->evidencias->count());

        // Desglose por tipo de actividad
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

        // Desglose por materia / grupo
        $porMateria = $grupos->map(function (Grupo $grupo) use ($actividades) {
            $actsGrupo = $actividades->filter(fn ($a) => (int) $a->grupo_id === (int) $grupo->id || $a->grupos->contains('id', $grupo->id));

            return [
                'grupo' => $grupo,
                'codigo' => $grupo->asignatura?->codigo,
                'asignatura' => $grupo->asignatura?->nombre,
                'numero_grupo' => $grupo->numero_grupo,
                'programa' => $grupo->asignatura?->programaCurricular?->nombre,
                'estudiantes_inscritos' => $grupo->numero_estudiantes,
                'cantidad_actividades' => $actsGrupo->count(),
                'horas_acumuladas' => (float) $actsGrupo->sum('duracion_horas'),
                'evidencias_acumuladas' => (int) $actsGrupo->sum(fn ($a) => $a->evidencias->count()),
            ];
        });

        // Criterios de acreditación tributados
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
            'profesor' => $profesor,
            'periodo' => $periodo,
            'grupos' => $grupos,
            'actividades' => $actividades,
            'totales' => [
                'materias' => $totalMaterias,
                'grupos' => $totalGrupos,
                'actividades' => $totalActividades,
                'registradas' => $totalRegistradas,
                'borrador' => $totalBorrador,
                'horas' => $totalHoras,
                'estudiantes' => $totalEstudiantes,
                'evidencias' => $totalEvidencias,
            ],
            'por_tipo' => $porTipo,
            'por_materia' => $porMateria,
            'criterios' => $criterios,
        ];
    }
}
