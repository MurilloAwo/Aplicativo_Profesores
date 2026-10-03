<?php

namespace App\Http\Requests\Admin;

use App\Models\PeriodoAcademico;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePeriodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:10', 'unique:periodos_academicos,codigo'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
