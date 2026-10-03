<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Grupos y Materias</h2>
            <a href="{{ route('admin.grupos.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nuevo Grupo
            </a>
        </div>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Filtros por periodo y profesor -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
            <form method="GET" action="{{ route('admin.grupos.index') }}" class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="w-full sm:w-64">
                    <x-input-label for="periodo_id" value="Filtrar por Periodo" class="text-xs text-gray-500 mb-1" />
                    <select id="periodo_id" name="periodo_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500" onchange="this.form.submit()">
                        @foreach ($periodos as $per)
                            <option value="{{ $per->id }}" {{ $periodoSeleccionadoId == $per->id ? 'selected' : '' }}>
                                {{ $per->codigo }} {{ $per->activo ? '(Vigente)' : '' }} {{ $per->cerrado ? '[Cerrado]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-72">
                    <x-input-label for="profesor_id" value="Filtrar por Docente" class="text-xs text-gray-500 mb-1" />
                    <select id="profesor_id" name="profesor_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Todos los docentes</option>
                        @foreach ($profesores as $prof)
                            <option value="{{ $prof->id }}" {{ request('profesor_id') == $prof->id ? 'selected' : '' }}>
                                {{ $prof->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 self-end">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">Filtrar</button>
                    @if (request()->filled('profesor_id'))
                        <a href="{{ route('admin.grupos.index', ['periodo_id' => $periodoSeleccionadoId]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Asignatura</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Grupo</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Profesor Asignado</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Modalidad</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Horario</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Estudiantes</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Actividades</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($grupos as $g)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $g->asignatura?->nombre }}</div>
                                    <div class="text-xs text-gray-500">Cód: {{ $g->asignatura?->codigo }} — {{ $g->asignatura?->programaCurricular?->nombre }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-emerald-800">
                                    G-{{ $g->numero_grupo }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $g->profesor?->name ?? 'Sin asignar' }}</div>
                                    <div class="text-xs text-gray-500">{{ $g->profesor?->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ ucfirst($g->modalidad) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-xs">{{ $g->horario ?? 'Por definir' }}</td>
                                <td class="px-6 py-4 text-center text-gray-800 font-semibold">{{ $g->numero_estudiantes }}</td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $g->actividades_principales_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.grupos.edit', $g) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                    @if ($g->actividades_principales_count === 0)
                                        <form method="POST" action="{{ route('admin.grupos.destroy', $g) }}" class="inline" onsubmit="return confirm('¿Eliminar este grupo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">No hay grupos registrados en este periodo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($grupos->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $grupos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
