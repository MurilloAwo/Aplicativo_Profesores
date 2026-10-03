<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Rules\SafeEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $id = $usuario instanceof User ? $usuario->id : $usuario;

        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'documento' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'string', 'max:255', new SafeEmailRule, Rule::unique('users')->ignore($id)],
            'password' => ['nullable', Password::defaults()],
            'rol' => ['required', Rule::in([User::ROL_ADMIN, User::ROL_PROFESOR])],
            'tipo_vinculacion' => ['nullable', Rule::in(['planta', 'ocasional', 'catedra'])],
            'dedicacion' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:50'],
        ];
    }
}
