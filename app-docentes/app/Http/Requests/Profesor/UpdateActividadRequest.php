<?php

namespace App\Http\Requests\Profesor;

use App\Models\Actividad;
use App\Models\TipoActividad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actividad = $this->route('actividad');

        return $actividad && $this->user()->can('update', $actividad);
    }

    public function rules(): array
    {
        return [
            'tipo_actividad_id' => ['required', 'exists:tipos_actividad,id'],
            'entidad_externa_id' => [
                'nullable',
                'exists:entidades_externas,id',
                function ($attribute, $value, $fail) {
                    $tipo = TipoActividad::find($this->input('tipo_actividad_id'));
                    if ($tipo && $tipo->requiere_entidad_externa && empty($value)) {
                        $fail('El tipo de actividad seleccionado requiere indicar una entidad externa.');
                    }
                },
            ],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'objetivo' => ['nullable', 'string'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'duracion_horas' => ['required', 'numeric', 'min:0.1', 'max:999.99'],
            'lugar' => ['nullable', 'string', 'max:255'],
            'modalidad' => ['required', Rule::in(['presencial', 'virtual', 'hibrida'])],
            'numero_estudiantes_participantes' => ['required', 'integer', 'min:0'],
            'nombre_invitado' => ['nullable', 'string', 'max:255'],
            'cargo_invitado' => ['nullable', 'string', 'max:255'],
            'resultados_obtenidos' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'estado' => ['required', Rule::in([Actividad::ESTADO_BORRADOR, Actividad::ESTADO_REGISTRADA])],
            'grupos_adicionales' => ['nullable', 'array'],
            'grupos_adicionales.*' => ['integer', 'exists:grupos,id'],
            'criterios' => ['nullable', 'array'],
            'criterios.*' => ['integer', 'exists:criterios_acreditacion,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_actividad_id.required' => 'El tipo de actividad es obligatorio.',
            'titulo.required' => 'El título de la actividad es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.',
            'duracion_horas.required' => 'La duración en horas es obligatoria.',
            'duracion_horas.min' => 'La duración en horas debe ser de al menos 0.1 horas.',
            'modalidad.required' => 'La modalidad es obligatoria.',
            'numero_estudiantes_participantes.required' => 'El número de estudiantes participantes es obligatorio.',
            'estado.required' => 'El estado de la actividad es obligatorio.',
        ];
    }
}
