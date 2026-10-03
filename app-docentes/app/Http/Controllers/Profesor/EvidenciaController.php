<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profesor\StoreEvidenciaRequest;
use App\Models\Actividad;
use App\Models\Evidencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class EvidenciaController extends Controller
{
    public function store(StoreEvidenciaRequest $request, Actividad $actividad): RedirectResponse
    {
        $file = $request->file('archivo');
        $path = $file->store('evidencias', 'local');

        $actividad->evidencias()->create([
            'tipo' => $request->input('tipo'),
            'nombre_original' => $file->getClientOriginalName(),
            'ruta' => $path,
            'mime' => $file->getClientMimeType() ?: ($file->getMimeType() ?: 'application/octet-stream'),
            'tamano' => $file->getSize(),
        ]);

        return back()->with('success', 'Evidencia subida y almacenada de forma segura.');
    }

    public function download(Request $request, Evidencia $evidencia): Response
    {
        $this->authorize('view', $evidencia);

        if (! Storage::disk('local')->exists($evidencia->ruta)) {
            abort(404, 'El archivo de evidencia solicitado no existe en el almacenamiento.');
        }

        if ($request->boolean('inline')) {
            return Storage::disk('local')->response(
                $evidencia->ruta,
                $evidencia->nombre_original,
                ['Content-Type' => $evidencia->mime]
            );
        }

        return Storage::disk('local')->download(
            $evidencia->ruta,
            $evidencia->nombre_original,
            ['Content-Type' => $evidencia->mime]
        );
    }

    public function destroy(Evidencia $evidencia): RedirectResponse
    {
        $this->authorize('delete', $evidencia);

        if (Storage::disk('local')->exists($evidencia->ruta)) {
            Storage::disk('local')->delete($evidencia->ruta);
        }

        $evidencia->delete();

        return back()->with('success', 'Evidencia eliminada correctamente.');
    }
}
