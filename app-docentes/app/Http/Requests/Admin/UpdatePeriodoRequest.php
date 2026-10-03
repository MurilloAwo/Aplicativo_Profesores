<?php

namespace App\Http\Requests\Admin;

use App\Models\PeriodoAcademico;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePeriodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $periodo = $this->route('periodo');
        $id = $periodo instanceof PeriodoAcademico ? $periodo->id : $periodo;

        return [
            'codigo' => ['required', 'string', 'max:10', Rule::unique('periodos_academicos')->ignore($id)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
