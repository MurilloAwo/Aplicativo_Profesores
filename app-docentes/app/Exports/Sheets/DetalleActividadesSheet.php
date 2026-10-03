<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetalleActividadesSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly Collection $actividades)
    {
    }

    public function collection(): Collection
    {
        return $this->actividades->map(function ($act) {
            $invitado = $act->nombre_invitado
                ? $act->nombre_invitado.($act->cargo_invitado ? ' ('.$act->cargo_invitado.')' : '')
                : 'N/A';

            $gruposTexto = $act->grupos->map(fn ($g) => $g->asignatura?->codigo.'-G'.$g->numero_grupo)->join(', ');

            return [
                'fecha' => $act->fecha_inicio?->format('d/m/Y'),
                'titulo' => $act->titulo,
                'tipo' => $act->tipoActividad?->nombre ?? 'N/A',
                'materia_principal' => ($act->grupo?->asignatura?->codigo ?? '').' - G'.($act->grupo?->numero_grupo ?? ''),
                'multigrupos' => $gruposTexto ?: 'N/A',
                'modalidad' => ucfirst($act->modalidad),
                'duracion' => (float) $act->duracion_horas,
                'estudiantes' => (int) $act->numero_estudiantes_participantes,
                'entidad' => $act->entidadExterna?->nombre ?? 'N/A',
                'invitado' => $invitado,
                'estado' => ucfirst($act->estado),
                'evidencias' => $act->evidencias->count(),
                'criterios' => $act->criterios->pluck('codigo')->join(', ') ?: 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Título de la Actividad',
            'Tipo de Actividad',
            'Grupo Principal',
            'Grupos Cubiertos',
            'Modalidad',
            'Duración (Horas)',
            'Estudiantes',
            'Entidad Externa',
            'Invitado / Conferencista',
            'Estado',
            'Evidencias Adjuntas',
            'Criterios de Acreditación',
        ];
    }

    public function title(): string
    {
        return 'Detalle de Actividades';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']]],
        ];
    }
}
