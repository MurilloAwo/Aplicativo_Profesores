<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCriterioRequest;
use App\Http\Requests\Admin\UpdateCriterioRequest;
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

    public function store(StoreCriterioRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        CriterioAcreditacion::create($validated);

        return redirect()->route('admin.criterios.index')
            ->with('success', 'Criterio de acreditación registrado.');
    }

    public function edit(CriterioAcreditacion $criterio): View
    {
        return view('admin.criterios.edit', compact('criterio'));
    }

    public function update(UpdateCriterioRequest $request, CriterioAcreditacion $criterio): RedirectResponse
    {
        $validated = $request->validated();

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
