<?php

namespace App\Exports;

use App\Exports\Sheets\CriteriosAcreditacionSheet;
use App\Exports\Sheets\DetalleActividadesSheet;
use App\Exports\Sheets\ResumenMateriasSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ResumenSemestralExport implements WithMultipleSheets
{
    public function __construct(private readonly array $resumen)
    {
    }

    public function sheets(): array
    {
        return [
            new ResumenMateriasSheet(collect($this->resumen['por_materia'])),
            new DetalleActividadesSheet(collect($this->resumen['actividades'])),
            new CriteriosAcreditacionSheet(collect($this->resumen['criterios'])),
        ];
    }
}
