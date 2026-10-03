<?php

namespace App\Http\Requests\Admin;

use App\Models\EntidadExterna;
use Illuminate\Foundation\Http\FormRequest;

class StoreEntidadExternaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'string', 'max:20', 'unique:entidades_externas,nit'],
            'sector' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'contacto' => ['nullable', 'string', 'max:255'],
        ];
    }
}
