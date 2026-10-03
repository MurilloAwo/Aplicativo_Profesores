<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ConsolidadoDepartamentoExport;
use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use App\Services\ConsolidadoDepartamentoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConsolidadoDepartamentoController extends Controller
{
    public function __construct(private readonly ConsolidadoDepartamentoService $service)
    {
    }

    public function index(Request $request): View
    {
        $periodos = PeriodoAcademico::orderByDesc('fecha_inicio')->get();

        $periodoId = $request->input('periodo_id');
        $periodo = $periodoId ? PeriodoAcademico::find($periodoId) : PeriodoAcademico::actual();

        if (! $periodo && $periodos->isNotEmpty()) {
            $periodo = $periodos->first();
        }

        $consolidado = $periodo
            ? $this->service->obtenerConsolidadoPeriodo($periodo)
            : [
                'totales' => [
                    'docentes_activos' => 0, 'grupos_ofertados' => 0, 'asignaturas_distintas' => 0,
                    'actividades' => 0, 'registradas' => 0, 'borrador' => 0, 'horas' => 0,
                    'estudiantes' => 0, 'evidencias' => 0,
                ],
                'por_docente' => collect(),
                'por_tipo' => collect(),
                'por_programa' => collect(),
                'actividades' => collect(),
                'criterios' => collect(),
            ];

        return view('admin.consolidado.index', [
            'periodos' => $periodos,
            'periodo' => $periodo,
            'totales' => $consolidado['totales'],
            'por_docente' => $consolidado['por_docente'],
            'por_tipo' => $consolidado['por_tipo'],
            'por_programa' => $consolidado['por_programa'],
            'actividades' => $consolidado['actividades'],
            'criterios' => $consolidado['criterios'],
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $periodo = $request->filled('periodo_id')
            ? PeriodoAcademico::findOrFail($request->input('periodo_id'))
            : (PeriodoAcademico::actual() ?? abort(404, 'No hay período académico activo disponible.'));

        $consolidado = $this->service->obtenerConsolidadoPeriodo($periodo);

        $pdf = Pdf::loadView('admin.consolidado.pdf', $consolidado)
            ->setPaper('letter', 'portrait')
            ->setOption('isRemoteEnabled', true);

        $nombreArchivo = "Consolidado_Departamento_{$periodo->codigo}.pdf";

        return $pdf->download($nombreArchivo);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $periodo = $request->filled('periodo_id')
            ? PeriodoAcademico::findOrFail($request->input('periodo_id'))
            : (PeriodoAcademico::actual() ?? abort(404, 'No hay período académico activo disponible.'));

        $consolidado = $this->service->obtenerConsolidadoPeriodo($periodo);

        $nombreArchivo = "Consolidado_Departamento_{$periodo->codigo}.xlsx";

        return Excel::download(new ConsolidadoDepartamentoExport($consolidado), $nombreArchivo);
    }
}
