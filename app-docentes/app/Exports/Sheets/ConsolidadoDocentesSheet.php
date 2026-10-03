<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsolidadoDocentesSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly Collection $porDocente)
    {
    }

    public function collection(): Collection
    {
        return $this->porDocente->map(function ($item) {
            return [
                'nombre' => $item['nombre'],
                'documento' => $item['documento'],
                'dedicacion' => $item['dedicacion'],
                'categoria' => $item['categoria'],
                'materias' => $item['materias_count'],
                'grupos' => $item['grupos_count'],
                'actividades' => $item['actividades_count'],
                'registradas' => $item['registradas_count'],
                'horas' => $item['horas_totales'],
                'estudiantes' => $item['estudiantes_impactados'],
                'evidencias' => $item['evidencias_count'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Docente',
            'Documento de Identidad',
            'Dedicación',
            'Categoría',
            'Materias Impartidas',
            'Grupos Asignados',
            'Total Actividades',
            'Actividades Registradas',
            'Horas Totales',
            'Estudiantes Impactados',
            'Evidencias Adjuntas',
        ];
    }

    public function title(): string
    {
        return 'Consolidado Docentes';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '581C87']]],
        ];
    }
}
