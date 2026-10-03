<?php

namespace App\Http\Requests\Admin;

use App\Models\TipoActividad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $tipo = $this->route('tipos_actividad');
        $id = $tipo instanceof TipoActividad ? $tipo->id : $tipo;

        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('tipos_actividad')->ignore($id)],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', Rule::in(['docencia', 'extension', 'investigacion', 'gestion'])],
            'requiere_entidad_externa' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
