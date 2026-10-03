<?php

namespace App\Http\Requests\Admin;

use App\Models\ProgramaCurricular;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $programa = $this->route('programa');
        $id = $programa instanceof ProgramaCurricular ? $programa->id : $programa;

        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('programas_curriculares')->ignore($id)],
            'nombre' => ['required', 'string', 'max:255'],
            'nivel' => ['required', Rule::in(['pregrado', 'posgrado'])],
        ];
    }
}
