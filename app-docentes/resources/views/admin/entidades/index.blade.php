<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Entidades Externas (Empresas y Aliados)</h2>
            <a href="{{ route('admin.entidades.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nueva Entidad
            </a>
        </div>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Búsqueda -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
            <form method="GET" action="{{ route('admin.entidades.index') }}" class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="flex-1 w-full">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, NIT o ciudad..."
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">Buscar</button>
                    @if (request()->filled('buscar'))
                        <a href="{{ route('admin.entidades.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Nombre de la Entidad</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">NIT</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Sector</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Ciudad</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Contacto</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Actividades</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($entidades as $ent)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $ent->nombre }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $ent->nit ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $ent->sector ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $ent->ciudad ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600 text-xs">{{ $ent->contacto ?? '—' }}</td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $ent->actividades_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.entidades.edit', $ent) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                    @if ($ent->actividades_count === 0)
                                        <form method="POST" action="{{ route('admin.entidades.destroy', $ent) }}" class="inline" onsubmit="return confirm('¿Eliminar esta entidad externa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No hay entidades externas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($entidades->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $entidades->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
