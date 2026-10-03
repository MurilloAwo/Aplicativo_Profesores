<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Criterios de Acreditación Institucional</h2>
                <p class="text-xs text-gray-500 mt-1">Catálogo configurable de factores y características del modelo de acreditación vigente.</p>
            </div>
            <a href="{{ route('admin.criterios.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nuevo Criterio
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
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Código</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Nombre / Factor</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Descripción</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Actividades Asociadas</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($criterios as $crit)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $crit->codigo }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $crit->nombre }}</td>
                                <td class="px-6 py-4 text-gray-600 text-xs">{{ $crit->descripcion ?? '—' }}</td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $crit->actividades_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.criterios.edit', $crit) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                    <form method="POST" action="{{ route('admin.criterios.destroy', $crit) }}" class="inline" onsubmit="return confirm('¿Eliminar este criterio de acreditación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No hay criterios de acreditación cargados. El administrador debe agregar los factores vigentes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($criterios->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $criterios->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
