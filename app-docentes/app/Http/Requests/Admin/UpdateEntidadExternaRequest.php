<?php

namespace App\Http\Requests\Admin;

use App\Models\EntidadExterna;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEntidadExternaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $entidad = $this->route('entidade') ?? $this->route('entidad');
        $id = $entidad instanceof EntidadExterna ? $entidad->id : $entidad;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'string', 'max:20', Rule::unique('entidades_externas')->ignore($id)],
            'sector' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'contacto' => ['nullable', 'string', 'max:255'],
        ];
    }
}
