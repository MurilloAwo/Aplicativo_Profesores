<?php

namespace App\Http\Requests\Admin;

use App\Models\CriterioAcreditacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCriterioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:30', 'unique:criterios_acreditacion,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
