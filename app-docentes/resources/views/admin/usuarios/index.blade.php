<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Usuarios</h2>
            <a href="{{ route('admin.usuarios.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nuevo Usuario
            </a>
        </div>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Filtros y búsqueda -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="flex-1 w-full">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombres, apellidos, documento o correo..."
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="w-full sm:w-48">
                    <select name="rol" class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Todos los roles</option>
                        <option value="profesor" {{ request('rol') === 'profesor' ? 'selected' : '' }}>Profesor</option>
                        <option value="admin" {{ request('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">Filtrar</button>
                    @if (request()->hasAny(['buscar', 'rol']))
                        <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Docente / Usuario</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Documento</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Rol</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Vinculación</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Estado</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($usuarios as $u)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $u->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $u->documento ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $u->esAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $u->esAdmin() ? 'Administrador' : 'Profesor' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $u->tipo_vinculacion ? ucfirst($u->tipo_vinculacion) : '—' }}
                                    @if ($u->dedicacion)
                                        <span class="text-xs text-gray-400 block">{{ $u->dedicacion }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.usuarios.toggle-activo', $u) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->activo ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}"
                                                title="Clic para cambiar estado">
                                            {{ $u->activo ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.usuarios.edit', $u) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No se encontraron usuarios.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($usuarios->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
