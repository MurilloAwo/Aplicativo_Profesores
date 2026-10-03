<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoActividad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TipoActividadController extends Controller
{
    public function index(): View
    {
        $tipos = TipoActividad::withCount('actividades')->orderBy('categoria')->orderBy('nombre')->paginate(15);

        return view('admin.tipos_actividad.index', compact('tipos'));
    }

    public function create(): View
    {
        return view('admin.tipos_actividad.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:tipos_actividad,nombre'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', Rule::in(array_keys(TipoActividad::CATEGORIAS))],
            'requiere_entidad_externa' => ['boolean'],
            'activo' => ['boolean'],
        ]);

        $validated['requiere_entidad_externa'] = $request->boolean('requiere_entidad_externa');
        $validated['activo'] = $request->boolean('activo', true);

        TipoActividad::create($validated);

        return redirect()->route('admin.tipos-actividad.index')
            ->with('success', 'Tipo de actividad registrado.');
    }

    public function edit(TipoActividad $tipos_actividad): View
    {
        $tipo = $tipos_actividad;

        return view('admin.tipos_actividad.edit', compact('tipo'));
    }

    public function update(Request $request, TipoActividad $tipos_actividad): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('tipos_actividad')->ignore($tipos_actividad->id)],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', Rule::in(array_keys(TipoActividad::CATEGORIAS))],
            'requiere_entidad_externa' => ['boolean'],
            'activo' => ['boolean'],
        ]);

        $validated['requiere_entidad_externa'] = $request->boolean('requiere_entidad_externa');
        $validated['activo'] = $request->boolean('activo');

        $tipos_actividad->update($validated);

        return redirect()->route('admin.tipos-actividad.index')
            ->with('success', 'Tipo de actividad actualizado.');
    }

    public function toggleActivo(TipoActividad $tipos_actividad): RedirectResponse
    {
        $tipos_actividad->activo = ! $tipos_actividad->activo;
        $tipos_actividad->save();

        $estado = $tipos_actividad->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Tipo '{$tipos_actividad->nombre}' {$estado}.");
    }
}
