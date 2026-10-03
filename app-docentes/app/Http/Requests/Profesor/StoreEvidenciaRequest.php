<?php

namespace App\Http\Requests\Profesor;

use App\Models\Evidencia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEvidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actividad = $this->route('actividad');

        return $actividad && $this->user()->can('create', [Evidencia::class, $actividad]);
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(array_keys(Evidencia::TIPOS))],
            'archivo' => [
                'required',
                'file',
                'max:10240', // 10 MB en kilobytes
                'mimes:jpeg,jpg,png,pdf,docx,xlsx',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'Debe seleccionar el tipo de evidencia.',
            'tipo.in' => 'El tipo de evidencia seleccionado no es válido.',
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.file' => 'El elemento subido debe ser un archivo válido.',
            'archivo.max' => 'El archivo no puede exceder los 10 MB de tamaño.',
            'archivo.mimes' => 'El archivo debe ser de formato JPG, PNG, PDF, DOCX o XLSX.',
        ];
    }
}
