<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResumenMateriasSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly Collection $porMateria)
    {
    }

    public function collection(): Collection
    {
        return $this->porMateria->map(function ($item) {
            return [
                'codigo' => $item['codigo'],
                'asignatura' => $item['asignatura'],
                'numero_grupo' => 'Grupo '.$item['numero_grupo'],
                'programa' => $item['programa'],
                'estudiantes_inscritos' => $item['estudiantes_inscritos'],
                'cantidad_actividades' => $item['cantidad_actividades'],
                'horas_acumuladas' => $item['horas_acumuladas'],
                'evidencias_acumuladas' => $item['evidencias_acumuladas'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código SIA',
            'Asignatura',
            'Grupo',
            'Programa Curricular',
            'Estudiantes Inscritos',
            'Total Actividades',
            'Horas Acumuladas',
            'Evidencias Adjuntas',
        ];
    }

    public function title(): string
    {
        return 'Materias y Grupos';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']]],
        ];
    }
}
