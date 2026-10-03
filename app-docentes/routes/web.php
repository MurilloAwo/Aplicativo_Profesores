<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AsignaturaController;
use App\Http\Controllers\Admin\CriterioAcreditacionController;
use App\Http\Controllers\Admin\EntidadExternaController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\PeriodoAcademicoController;
use App\Http\Controllers\Admin\ProgramaCurricularController;
use App\Http\Controllers\Admin\TipoActividadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Profesor\ActividadController;
use App\Http\Controllers\Profesor\EvidenciaController;
use App\Http\Controllers\Profesor\MateriaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de profesores: materias del periodo activo
Route::middleware(['auth', 'active', 'role:profesor'])->group(function () {
    Route::get('/mis-materias', [MateriaController::class, 'index'])->name('materias.index');
    Route::get('/mis-materias/{grupo}', [MateriaController::class, 'show'])->name('materias.show');
});

// Rutas de actividades y evidencias (profesor y administrador)
Route::middleware(['auth', 'active', 'role:profesor,admin'])->group(function () {
    Route::resource('actividades', ActividadController::class)->parameters(['actividades' => 'actividad']);
    Route::post('actividades/{actividad}/evidencias', [EvidenciaController::class, 'store'])->name('actividades.evidencias.store');
    Route::get('evidencias/{evidencia}/descargar', [EvidenciaController::class, 'download'])->name('evidencias.download');
    Route::delete('evidencias/{evidencia}', [EvidenciaController::class, 'destroy'])->name('evidencias.destroy');

    // Resumen semestral en pantalla, PDF y Excel
    Route::get('/resumen-semestral', [\App\Http\Controllers\Profesor\ResumenSemestralController::class, 'index'])->name('resumen.index');
    Route::get('/resumen-semestral/pdf', [\App\Http\Controllers\Profesor\ResumenSemestralController::class, 'exportPdf'])->name('resumen.pdf');
    Route::get('/resumen-semestral/excel', [\App\Http\Controllers\Profesor\ResumenSemestralController::class, 'exportExcel'])->name('resumen.excel');
});

// Rutas exclusivas de administración
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::patch('usuarios/{usuario}/toggle-activo', [UserController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
    Route::resource('usuarios', UserController::class)->parameters(['usuarios' => 'usuario']);

    Route::patch('periodos/{periodo}/activar', [PeriodoAcademicoController::class, 'activar'])->name('periodos.activar');
    Route::patch('periodos/{periodo}/toggle-cerrado', [PeriodoAcademicoController::class, 'toggleCerrado'])->name('periodos.toggle-cerrado');
    Route::resource('periodos', PeriodoAcademicoController::class);

    Route::resource('programas', ProgramaCurricularController::class);
    Route::resource('asignaturas', AsignaturaController::class);
    Route::resource('grupos', GrupoController::class);

    Route::patch('tipos-actividad/{tipos_actividad}/toggle-activo', [TipoActividadController::class, 'toggleActivo'])->name('tipos-actividad.toggle-activo');
    Route::resource('tipos-actividad', TipoActividadController::class);

    Route::resource('entidades', EntidadExternaController::class);
    Route::resource('criterios', CriterioAcreditacionController::class);
});

require __DIR__.'/auth.php';
