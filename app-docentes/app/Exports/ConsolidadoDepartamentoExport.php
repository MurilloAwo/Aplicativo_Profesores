<?php

namespace App\Exports;

use App\Exports\Sheets\ActividadesDepartamentoSheet;
use App\Exports\Sheets\ConsolidadoDocentesSheet;
use App\Exports\Sheets\CriteriosAcreditacionSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ConsolidadoDepartamentoExport implements WithMultipleSheets
{
    public function __construct(private readonly array $consolidado)
    {
    }

    public function sheets(): array
    {
        return [
            new ConsolidadoDocentesSheet(collect($this->consolidado['por_docente'])),
            new ActividadesDepartamentoSheet(collect($this->consolidado['actividades'])),
            new CriteriosAcreditacionSheet(collect($this->consolidado['criterios'])),
        ];
    }
}
