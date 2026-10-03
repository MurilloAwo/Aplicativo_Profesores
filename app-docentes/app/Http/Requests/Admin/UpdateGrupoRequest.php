<?php

namespace App\Http\Requests\Admin;

use App\Models\Grupo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGrupoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $grupo = $this->route('grupo');
        $id = $grupo instanceof Grupo ? $grupo->id : $grupo;

        return [
            'asignatura_id' => ['required', 'exists:asignaturas,id'],
            'periodo_academico_id' => ['required', 'exists:periodos_academicos,id'],
            'profesor_id' => ['required', 'exists:users,id'],
            'numero_grupo' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('grupos')->where(function ($query) {
                    return $query->where('asignatura_id', $this->input('asignatura_id'))
                        ->where('periodo_academico_id', $this->input('periodo_academico_id'));
                })->ignore($id),
            ],
            'modalidad' => ['required', Rule::in(['presencial', 'virtual', 'hibrida'])],
            'horario' => ['nullable', 'string', 'max:255'],
            'numero_estudiantes' => ['required', 'integer', 'min:0'],
        ];
    }
}
