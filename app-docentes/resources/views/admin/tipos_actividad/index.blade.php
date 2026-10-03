<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Catálogo de Tipos de Actividad</h2>
            <a href="{{ route('admin.tipos-actividad.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nuevo Tipo
            </a>
        </div>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Nombre / Descripción</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Categoría</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Requiere Entidad Externa</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Estado</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Actividades</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($tipos as $t)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $t->nombre }}</div>
                                    @if ($t->descripcion)
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $t->descripcion }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ $t->categoria_etiqueta }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($t->requiere_entidad_externa)
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">Sí</span>
                                    @else
                                        <span class="text-gray-400 text-xs">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.tipos-actividad.toggle-activo', $t) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $t->activo ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                            {{ $t->activo ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $t->actividades_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.tipos-actividad.edit', $t) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No hay tipos de actividad registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tipos->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $tipos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
