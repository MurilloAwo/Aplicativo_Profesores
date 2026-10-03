<?php

namespace App\Http\Requests\Admin;

use App\Models\ProgramaCurricular;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:20', 'unique:programas_curriculares,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'nivel' => ['required', Rule::in(['pregrado', 'posgrado'])],
        ];
    }
}
