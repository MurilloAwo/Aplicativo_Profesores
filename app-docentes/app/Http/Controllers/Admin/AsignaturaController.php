<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:asignaturas,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:1', 'max:10'],
            'tipologia' => ['nullable', 'string', 'max:60'],
            'programa_curricular_id' => ['required', 'exists:programas_curriculares,id'],
        ]);

        Asignatura::create($validated);

        return redirect()->route('admin.asignaturas.index')
            ->with('success', 'Asignatura registrada exitosamente.');
    }

    public function edit(Asignatura $asignatura): View
    {
        $programas = ProgramaCurricular::orderBy('nombre')->get();

        return view('admin.asignaturas.edit', compact('asignatura', 'programas'));
    }

    public function update(Request $request, Asignatura $asignatura): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('asignaturas')->ignore($asignatura->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:1', 'max:10'],
            'tipologia' => ['nullable', 'string', 'max:60'],
            'programa_curricular_id' => ['required', 'exists:programas_curriculares,id'],
        ]);

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
