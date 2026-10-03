<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CriterioAcreditacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CriterioAcreditacionController extends Controller
{
    public function index(): View
    {
        $criterios = CriterioAcreditacion::withCount('actividades')->orderBy('codigo')->paginate(15);

        return view('admin.criterios.index', compact('criterios'));
    }

    public function create(): View
    {
        return view('admin.criterios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:criterios_acreditacion,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ]);

        CriterioAcreditacion::create($validated);

        return redirect()->route('admin.criterios.index')
            ->with('success', 'Criterio de acreditación registrado.');
    }

    public function edit(CriterioAcreditacion $criterio): View
    {
        return view('admin.criterios.edit', compact('criterio'));
    }

    public function update(Request $request, CriterioAcreditacion $criterio): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:30', Rule::unique('criterios_acreditacion')->ignore($criterio->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $criterio->update($validated);

        return redirect()->route('admin.criterios.index')
            ->with('success', 'Criterio de acreditación actualizado.');
    }

    public function destroy(CriterioAcreditacion $criterio): RedirectResponse
    {
        $criterio->actividades()->detach();
        $criterio->delete();

        return redirect()->route('admin.criterios.index')
            ->with('success', 'Criterio de acreditación eliminado.');
    }
}
