<?php

namespace App\Http\Requests\Admin;

use App\Models\Asignatura;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAsignaturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'programa_curricular_id' => ['required', 'exists:programas_curriculares,id'],
            'codigo' => ['required', 'string', 'max:20', 'unique:asignaturas,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:0', 'max:30'],
            'tipologia' => ['nullable', 'string', 'max:100'],
        ];
    }
}
