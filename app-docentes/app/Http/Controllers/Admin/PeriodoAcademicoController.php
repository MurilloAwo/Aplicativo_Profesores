<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeriodoAcademicoController extends Controller
{
    public function index(): View
    {
        $periodos = PeriodoAcademico::withCount('grupos')->orderBy('codigo', 'desc')->paginate(10);

        return view('admin.periodos.index', compact('periodos'));
    }

    public function create(): View
    {
        return view('admin.periodos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:10', 'unique:periodos_academicos,codigo'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
        ]);

        $periodo = PeriodoAcademico::create([
            'codigo' => $validated['codigo'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'activo' => false,
            'cerrado' => false,
        ]);

        return redirect()->route('admin.periodos.index')
            ->with('success', "Periodo {$periodo->codigo} creado exitosamente.");
    }

    public function edit(PeriodoAcademico $periodo): View
    {
        return view('admin.periodos.edit', compact('periodo'));
    }

    public function update(Request $request, PeriodoAcademico $periodo): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:10', Rule::unique('periodos_academicos')->ignore($periodo->id)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
        ]);

        $periodo->update($validated);

        return redirect()->route('admin.periodos.index')
            ->with('success', "Periodo {$periodo->codigo} actualizado.");
    }

    /**
     * Regla de negocio: activa este periodo y desactiva todos los demás (solo un periodo activo).
     */
    public function activar(PeriodoAcademico $periodo): RedirectResponse
    {
        DB::transaction(function () use ($periodo) {
            PeriodoAcademico::where('id', '!=', $periodo->id)->update(['activo' => false]);
            $periodo->update(['activo' => true]);
        });

        return back()->with('success', "El periodo {$periodo->codigo} es ahora el periodo activo único.");
    }

    /**
     * Alternar estado de cerrado (cerrar / reabrir).
     */
    public function toggleCerrado(PeriodoAcademico $periodo): RedirectResponse
    {
        $periodo->cerrado = ! $periodo->cerrado;
        $periodo->save();

        $estado = $periodo->cerrado ? 'cerrado (modo solo lectura)' : 'reabierto (permite registro)';

        return back()->with('success', "El periodo {$periodo->codigo} fue {$estado}.");
    }
}
