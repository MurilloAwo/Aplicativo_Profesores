<?php

namespace App\Http\Requests\Admin;

use App\Models\Asignatura;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAsignaturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $asignatura = $this->route('asignatura');
        $id = $asignatura instanceof Asignatura ? $asignatura->id : $asignatura;

        return [
            'programa_curricular_id' => ['required', 'exists:programas_curriculares,id'],
            'codigo' => ['required', 'string', 'max:20', Rule::unique('asignaturas')->ignore($id)],
            'nombre' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:0', 'max:30'],
            'tipologia' => ['nullable', 'string', 'max:100'],
        ];
    }
}
