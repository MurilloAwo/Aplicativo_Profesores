<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramaCurricular;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgramaCurricularController extends Controller
{
    public function index(): View
    {
        $programas = ProgramaCurricular::withCount('asignaturas')->orderBy('codigo')->paginate(10);

        return view('admin.programas.index', compact('programas'));
    }

    public function create(): View
    {
        return view('admin.programas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:programas_curriculares,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'nivel' => ['required', Rule::in(['pregrado', 'posgrado'])],
        ]);

        ProgramaCurricular::create($validated);

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa curricular registrado exitosamente.');
    }

    public function edit(ProgramaCurricular $programa): View
    {
        return view('admin.programas.edit', compact('programa'));
    }

    public function update(Request $request, ProgramaCurricular $programa): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('programas_curriculares')->ignore($programa->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'nivel' => ['required', Rule::in(['pregrado', 'posgrado'])],
        ]);

        $programa->update($validated);

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa curricular actualizado.');
    }

    public function destroy(ProgramaCurricular $programa): RedirectResponse
    {
        if ($programa->asignaturas()->exists()) {
            return back()->with('error', 'No se puede eliminar el programa porque tiene asignaturas asociadas.');
        }

        $programa->delete();

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa curricular eliminado.');
    }
}
