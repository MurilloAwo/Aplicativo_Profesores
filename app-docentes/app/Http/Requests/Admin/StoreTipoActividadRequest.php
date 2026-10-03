<?php

namespace App\Http\Requests\Admin;

use App\Models\TipoActividad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTipoActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:tipos_actividad,nombre'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', Rule::in(['docencia', 'extension', 'investigacion', 'gestion'])],
            'requiere_entidad_externa' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
