<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Usuario: {{ $usuario->name }}</h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nombres" value="Nombres *" />
                        <x-text-input id="nombres" name="nombres" type="text" class="mt-1 block w-full" :value="old('nombres', $usuario->nombres)" required autofocus />
                        <x-input-error :messages="$errors->get('nombres')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="apellidos" value="Apellidos *" />
                        <x-text-input id="apellidos" name="apellidos" type="text" class="mt-1 block w-full" :value="old('apellidos', $usuario->apellidos)" required />
                        <x-input-error :messages="$errors->get('apellidos')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="documento" value="Documento de Identidad *" />
                        <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full" :value="old('documento', $usuario->documento)" required />
                        <x-input-error :messages="$errors->get('documento')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Correo Institucional *" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $usuario->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" value="Nueva Contraseña (dejar en blanco para conservar)" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="rol" value="Rol en el sistema *" />
                        <select id="rol" name="rol" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="profesor" {{ old('rol', $usuario->rol) === 'profesor' ? 'selected' : '' }}>Profesor</option>
                            <option value="admin" {{ old('rol', $usuario->rol) === 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        <x-input-error :messages="$errors->get('rol')" class="mt-2" />
                    </div>
                </div>

                <div class="border-t pt-4 mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Información Institucional del Docente</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="tipo_vinculacion" value="Tipo de Vinculación" />
                            <select id="tipo_vinculacion" name="tipo_vinculacion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">(No aplica / Seleccionar)</option>
                                <option value="planta" {{ old('tipo_vinculacion', $usuario->tipo_vinculacion) === 'planta' ? 'selected' : '' }}>Planta</option>
                                <option value="ocasional" {{ old('tipo_vinculacion', $usuario->tipo_vinculacion) === 'ocasional' ? 'selected' : '' }}>Ocasional</option>
                                <option value="catedra" {{ old('tipo_vinculacion', $usuario->tipo_vinculacion) === 'catedra' ? 'selected' : '' }}>Cátedra</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo_vinculacion')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="dedicacion" value="Dedicación" />
                            <x-text-input id="dedicacion" name="dedicacion" type="text" class="mt-1 block w-full" :value="old('dedicacion', $usuario->dedicacion)" />
                            <x-input-error :messages="$errors->get('dedicacion')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="categoria" value="Categoría" />
                            <x-text-input id="categoria" name="categoria" type="text" class="mt-1 block w-full" :value="old('categoria', $usuario->categoria)" />
                            <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">Actualizar Usuario</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
