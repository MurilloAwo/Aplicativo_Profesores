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
Route::middleware(['auth', 'role:profesor'])->group(function () {
    Route::get('/mis-materias', [\App\Http\Controllers\Profesor\MateriaController::class, 'index'])->name('materias.index');
    Route::get('/mis-materias/{grupo}', [\App\Http\Controllers\Profesor\MateriaController::class, 'show'])->name('materias.show');
});

// Rutas exclusivas de administración
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
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
