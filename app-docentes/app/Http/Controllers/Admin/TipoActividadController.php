<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTipoActividadRequest;
use App\Http\Requests\Admin\UpdateTipoActividadRequest;
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

    public function store(StoreTipoActividadRequest $request): RedirectResponse
    {
        $validated = $request->validated();
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

    public function update(UpdateTipoActividadRequest $request, TipoActividad $tipos_actividad): RedirectResponse
    {
        $validated = $request->validated();
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
