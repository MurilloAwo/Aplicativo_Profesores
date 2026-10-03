<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CriteriosAcreditacionSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly Collection $criterios)
    {
    }

    public function collection(): Collection
    {
        return $this->criterios->map(function ($item) {
            return [
                'codigo' => $item['codigo'],
                'nombre' => $item['nombre'],
                'cantidad' => $item['cantidad_actividades'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código Criterio',
            'Nombre del Criterio / Factor de Acreditación',
            'Actividades Vinculadas en el Semestre',
        ];
    }

    public function title(): string
    {
        return 'Criterios de Acreditación';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']]],
        ];
    }
}
