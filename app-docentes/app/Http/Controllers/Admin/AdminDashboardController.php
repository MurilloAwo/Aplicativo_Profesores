<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignatura;
use App\Models\CriterioAcreditacion;
use App\Models\EntidadExterna;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\ProgramaCurricular;
use App\Models\TipoActividad;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $periodoActivo = PeriodoAcademico::actual();

        return view('admin.dashboard', [
            'periodoActivo' => $periodoActivo,
            'totalDocentes' => User::where('rol', User::ROL_PROFESOR)->count(),
            'totalDocentesActivos' => User::where('rol', User::ROL_PROFESOR)->where('activo', true)->count(),
            'totalProgramas' => ProgramaCurricular::count(),
            'totalAsignaturas' => Asignatura::count(),
            'totalGruposActivos' => $periodoActivo ? Grupo::where('periodo_academico_id', $periodoActivo->id)->count() : 0,
            'totalTiposActividad' => TipoActividad::count(),
            'totalEntidades' => EntidadExterna::count(),
            'totalCriterios' => CriterioAcreditacion::count(),
        ]);
    }
}
