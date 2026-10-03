<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAsignaturaRequest;
use App\Http\Requests\Admin\UpdateAsignaturaRequest;
use App\Models\Asignatura;
use App\Models\ProgramaCurricular;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AsignaturaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asignatura::with('programaCurricular')->withCount('grupos');

        if ($request->filled('programa_id')) {
            $query->where('programa_curricular_id', $request->input('programa_id'));
        }

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        }

        $asignaturas = $query->orderBy('nombre')->paginate(15)->withQueryString();
        $programas = ProgramaCurricular::orderBy('nombre')->get();

        return view('admin.asignaturas.index', compact('asignaturas', 'programas'));
    }

    public function create(): View
    {
        $programas = ProgramaCurricular::orderBy('nombre')->get();

        return view('admin.asignaturas.create', compact('programas'));
    }

    public function store(StoreAsignaturaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Asignatura::create($validated);

        return redirect()->route('admin.asignaturas.index')
            ->with('success', 'Asignatura registrada exitosamente.');
    }

    public function edit(Asignatura $asignatura): View
    {
        $programas = ProgramaCurricular::orderBy('nombre')->get();

        return view('admin.asignaturas.edit', compact('asignatura', 'programas'));
    }

    public function update(UpdateAsignaturaRequest $request, Asignatura $asignatura): RedirectResponse
    {
        $validated = $request->validated();

        $asignatura->update($validated);

        return redirect()->route('admin.asignaturas.index')
            ->with('success', 'Asignatura actualizada.');
    }

    public function destroy(Asignatura $asignatura): RedirectResponse
    {
        if ($asignatura->grupos()->exists()) {
            return back()->with('error', 'No se puede eliminar la asignatura porque tiene grupos creados.');
        }

        $asignatura->delete();

        return redirect()->route('admin.asignaturas.index')
            ->with('success', 'Asignatura eliminada.');
    }
}
