<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEntidadExternaRequest;
use App\Http\Requests\Admin\UpdateEntidadExternaRequest;
use App\Models\EntidadExterna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EntidadExternaController extends Controller
{
    public function index(Request $request): View
    {
        $query = EntidadExterna::withCount('actividades');

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('nit', 'like', "%{$buscar}%")
                    ->orWhere('ciudad', 'like', "%{$buscar}%");
            });
        }

        $entidades = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('admin.entidades.index', compact('entidades'));
    }

    public function create(): View
    {
        return view('admin.entidades.create');
    }

    public function store(StoreEntidadExternaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        EntidadExterna::create($validated);

        return redirect()->route('admin.entidades.index')
            ->with('success', 'Entidad externa registrada.');
    }

    public function edit(EntidadExterna $entidade): View
    {
        $entidad = $entidade;

        return view('admin.entidades.edit', compact('entidad'));
    }

    public function update(UpdateEntidadExternaRequest $request, EntidadExterna $entidade): RedirectResponse
    {
        $validated = $request->validated();

        $entidade->update($validated);

        return redirect()->route('admin.entidades.index')
            ->with('success', 'Entidad externa actualizada.');
    }

    public function destroy(EntidadExterna $entidade): RedirectResponse
    {
        if ($entidade->actividades()->exists()) {
            return back()->with('error', 'No se puede eliminar la entidad porque tiene actividades asociadas.');
        }

        $entidade->delete();

        return redirect()->route('admin.entidades.index')
            ->with('success', 'Entidad externa eliminada.');
    }
}
