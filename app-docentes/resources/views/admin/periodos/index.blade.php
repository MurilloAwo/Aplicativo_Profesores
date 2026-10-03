<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Periodos Académicos</h2>
            <a href="{{ route('admin.periodos.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nuevo Periodo
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
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Fechas</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Vigencia</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Modo de Edición</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Grupos</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($periodos as $p)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $p->codigo }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $p->fecha_inicio->format('d/m/Y') }} — {{ $p->fecha_fin->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($p->activo)
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Activo (Vigente)
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.periodos.activar', $p) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs text-gray-500 hover:text-emerald-700 underline" title="Fijar como único periodo activo">
                                                Activar este periodo
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.periodos.toggle-cerrado', $p) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $p->cerrado ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}"
                                                title="Clic para cambiar entre lectura y edición">
                                            {{ $p->cerrado ? 'Cerrado (Solo lectura)' : 'Abierto (Permite registro)' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $p->grupos_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.periodos.edit', $p) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No hay periodos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($periodos->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $periodos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
