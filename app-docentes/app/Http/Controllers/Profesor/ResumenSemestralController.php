<?php

namespace App\Http\Controllers\Profesor;

use App\Exports\ResumenSemestralExport;
use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use App\Services\ResumenAcademicoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResumenSemestralController extends Controller
{
    public function __construct(private readonly ResumenAcademicoService $service)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $periodos = PeriodoAcademico::orderByDesc('fecha_inicio')->get();

        $periodoId = $request->input('periodo_id');
        $periodo = $periodoId ? PeriodoAcademico::find($periodoId) : PeriodoAcademico::actual();

        if (! $periodo && $periodos->isNotEmpty()) {
            $periodo = $periodos->first();
        }

        $resumen = $periodo
            ? $this->service->obtenerResumenProfesor($user, $periodo)
            : [
                'totales' => ['materias' => 0, 'grupos' => 0, 'actividades' => 0, 'registradas' => 0, 'borrador' => 0, 'horas' => 0, 'estudiantes' => 0, 'evidencias' => 0],
                'por_materia' => collect(),
                'por_tipo' => collect(),
                'actividades' => collect(),
                'criterios' => collect(),
            ];

        return view('profesor.resumen.index', [
            'periodos' => $periodos,
            'periodo' => $periodo,
            'totales' => $resumen['totales'],
            'por_materia' => $resumen['por_materia'],
            'por_tipo' => $resumen['por_tipo'],
            'actividades' => $resumen['actividades'],
            'criterios' => $resumen['criterios'],
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $user = $request->user();
        $periodo = $request->filled('periodo_id')
            ? PeriodoAcademico::findOrFail($request->input('periodo_id'))
            : (PeriodoAcademico::actual() ?? abort(404, 'No hay período académico activo disponible.'));

        $resumen = $this->service->obtenerResumenProfesor($user, $periodo);

        $pdf = Pdf::loadView('pdf.resumen_semestral', $resumen)
            ->setPaper('letter', 'portrait')
            ->setOption('isRemoteEnabled', true);

        $slugDocente = Str::slug($user->name);
        $nombreArchivo = "Resumen_Docente_{$periodo->codigo}_{$slugDocente}.pdf";

        return $pdf->download($nombreArchivo);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        $periodo = $request->filled('periodo_id')
            ? PeriodoAcademico::findOrFail($request->input('periodo_id'))
            : (PeriodoAcademico::actual() ?? abort(404, 'No hay período académico activo disponible.'));

        $resumen = $this->service->obtenerResumenProfesor($user, $periodo);

        $slugDocente = Str::slug($user->name);
        $nombreArchivo = "Resumen_Docente_{$periodo->codigo}_{$slugDocente}.xlsx";

        return Excel::download(new ResumenSemestralExport($resumen), $nombreArchivo);
    }
}
