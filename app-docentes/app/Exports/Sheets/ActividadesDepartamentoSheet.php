<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActividadesDepartamentoSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
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

            return [
                'fecha' => $act->fecha_inicio?->format('d/m/Y'),
                'docente' => $act->grupo?->profesor?->name ?? 'N/A',
                'titulo' => $act->titulo,
                'asignatura' => $act->grupo?->asignatura?->nombre ?? 'N/A',
                'grupo' => 'G'.($act->grupo?->numero_grupo ?? ''),
                'tipo' => $act->tipoActividad?->nombre ?? 'N/A',
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
            'Docente Responsable',
            'Título de la Actividad',
            'Asignatura',
            'Grupo',
            'Tipo de Actividad',
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
        return 'Actividades del Departamento';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '581C87']]],
        ];
    }
}
