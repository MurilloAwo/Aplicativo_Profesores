<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%")
                    ->orWhere('documento', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->input('rol'));
        }

        $usuarios = $query->orderBy('apellidos')->paginate(15)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'documento' => ['required', 'string', 'max:30', 'unique:users,documento'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'rol' => ['required', Rule::in([User::ROL_ADMIN, User::ROL_PROFESOR])],
            'tipo_vinculacion' => ['nullable', Rule::in(['planta', 'ocasional', 'catedra'])],
            'dedicacion' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:50'],
        ]);

        $user = new User([
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'documento' => $validated['documento'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tipo_vinculacion' => $validated['tipo_vinculacion'] ?? null,
            'dedicacion' => $validated['dedicacion'] ?? null,
            'categoria' => $validated['categoria'] ?? null,
        ]);
        $user->rol = $validated['rol'];
        $user->activo = true;
        $user->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', "Usuario {$user->name} creado exitosamente.");
    }

    public function edit(User $usuario): View
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'documento' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($usuario->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password' => ['nullable', Password::defaults()],
            'rol' => ['required', Rule::in([User::ROL_ADMIN, User::ROL_PROFESOR])],
            'tipo_vinculacion' => ['nullable', Rule::in(['planta', 'ocasional', 'catedra'])],
            'dedicacion' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:50'],
        ]);

        $usuario->fill([
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'documento' => $validated['documento'],
            'email' => $validated['email'],
            'tipo_vinculacion' => $validated['tipo_vinculacion'] ?? null,
            'dedicacion' => $validated['dedicacion'] ?? null,
            'categoria' => $validated['categoria'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }

        $usuario->rol = $validated['rol'];
        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', "Usuario {$usuario->name} actualizado exitosamente.");
    }

    public function toggleActivo(User $usuario): RedirectResponse
    {
        // Prevenir que un admin se desactive a sí mismo
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No puede desactivar su propia cuenta de administrador.');
        }

        $usuario->activo = ! $usuario->activo;
        $usuario->save();

        $estado = $usuario->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$usuario->name} {$estado} correctamente.");
    }
}
