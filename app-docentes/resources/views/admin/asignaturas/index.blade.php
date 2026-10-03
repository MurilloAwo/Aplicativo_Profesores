<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Asignaturas</h2>
            <a href="{{ route('admin.asignaturas.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nueva Asignatura
            </a>
        </div>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Filtros -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
            <form method="GET" action="{{ route('admin.asignaturas.index') }}" class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="flex-1 w-full">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por código o nombre de asignatura..."
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="w-full sm:w-64">
                    <select name="programa_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Todos los programas</option>
                        @foreach ($programas as $prog)
                            <option value="{{ $prog->id }}" {{ request('programa_id') == $prog->id ? 'selected' : '' }}>
                                {{ $prog->codigo }} - {{ $prog->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">Filtrar</button>
                    @if (request()->hasAny(['buscar', 'programa_id']))
                        <a href="{{ route('admin.asignaturas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Código</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Asignatura</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Programa</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Créditos</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Grupos</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($asignaturas as $asig)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $asig->codigo }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $asig->nombre }}
                                    @if ($asig->tipologia)
                                        <span class="text-xs text-gray-500 block">{{ $asig->tipologia }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $asig->programaCurricular?->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-center text-gray-800 font-semibold">{{ $asig->creditos }}</td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $asig->grupos_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.asignaturas.edit', $asig) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                    @if ($asig->grupos_count === 0)
                                        <form method="POST" action="{{ route('admin.asignaturas.destroy', $asig) }}" class="inline" onsubmit="return confirm('¿Eliminar esta asignatura?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No se encontraron asignaturas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($asignaturas->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $asignaturas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
