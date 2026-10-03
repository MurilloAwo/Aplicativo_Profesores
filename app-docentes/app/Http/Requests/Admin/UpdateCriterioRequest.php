<?php

namespace App\Http\Requests\Admin;

use App\Models\CriterioAcreditacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCriterioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $criterio = $this->route('criterio');
        $id = $criterio instanceof CriterioAcreditacion ? $criterio->id : $criterio;

        return [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('criterios_acreditacion')->ignore($id)],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
