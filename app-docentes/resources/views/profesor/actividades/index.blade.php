<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Actividades Docentes') }}
                </h2>
                <p class="text-xs text-gray-500">
                    Registro de charlas, visitas técnicas, proyectos de aula y evidencias académicas
                </p>
            </div>
            <div>
                @can('create', App\Models\Actividad::class)
                    <a href="{{ route('actividades.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition shadow">
                        + Nueva Actividad
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm mb-6 text-green-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm mb-6 text-amber-800 text-sm font-medium">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm mb-6 text-red-800 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('actividades.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Periodo Académico</label>
                    <select name="periodo_id" class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Todos los periodos</option>
                        @foreach ($periodos as $p)
                            <option value="{{ $p->id }}" {{ (string)$periodoId === (string)$p->id ? 'selected' : '' }}>
                                {{ $p->codigo }} {{ $p->activo ? '(Activo)' : '' }} {{ $p->cerrado ? '[Cerrado]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Materia / Grupo</label>
                    <select name="grupo_id" class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Todos mis grupos</option>
                        @foreach ($misGrupos as $g)
                            <option value="{{ $g->id }}" {{ request('grupo_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->asignatura?->codigo }} - G{{ $g->numero_grupo }}: {{ $g->asignatura?->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tipo de Actividad</label>
                    <select name="tipo_actividad_id" class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Todos los tipos</option>
                        @foreach ($tipos as $t)
                            <option value="{{ $t->id }}" {{ request('tipo_actividad_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Todos los estados</option>
                        <option value="registrada" {{ request('estado') === 'registrada' ? 'selected' : '' }}>Registrada</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 bg-emerald-700 text-white rounded text-xs font-semibold hover:bg-emerald-800 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('actividades.index') }}" class="py-2 px-3 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 transition text-center">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Listado de actividades -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if ($actividades->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-base font-semibold text-gray-700">No se encontraron actividades registradas</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Utilice el botón superior para crear una nueva actividad docente o modifique los filtros de búsqueda.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Actividad / Materia</th>
                                <th class="px-6 py-3 text-left font-semibold">Tipo</th>
                                <th class="px-6 py-3 text-center font-semibold">Fecha</th>
                                <th class="px-6 py-3 text-center font-semibold">Horas</th>
                                <th class="px-6 py-3 text-center font-semibold">Estudiantes</th>
                                <th class="px-6 py-3 text-center font-semibold">Evidencias</th>
                                <th class="px-6 py-3 text-center font-semibold">Estado</th>
                                <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($actividades as $act)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('actividades.show', $act) }}" class="font-bold text-gray-900 hover:text-emerald-700 transition">
                                            {{ $act->titulo }}
                                        </a>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $act->grupo?->asignatura?->nombre }} (G{{ $act->grupo?->numero_grupo }}) — {{ $act->grupo?->periodoAcademico?->codigo }}
                                        </div>
                                        @if ($act->grupos->count() > 1)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 mt-1">
                                                Cubre {{ $act->grupos->count() }} grupos
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="inline-flex px-2 py-0.5 rounded font-medium bg-gray-100 text-gray-800">
                                            {{ $act->tipoActividad?->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-gray-600">
                                        {{ $act->fecha_inicio->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs font-semibold text-gray-800">
                                        {{ $act->duracion_horas }} h
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-gray-800">
                                        {{ $act->numero_estudiantes_participantes }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs">
                                        <span class="inline-flex px-2 py-0.5 rounded font-medium {{ $act->evidencias->isNotEmpty() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $act->evidencias->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs">
                                        <span class="inline-flex px-2 py-0.5 rounded-full font-semibold {{ $act->estado === 'registrada' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($act->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 text-xs whitespace-nowrap">
                                        <a href="{{ route('actividades.show', $act) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold">Ver</a>

                                        @can('update', $act)
                                            <a href="{{ route('actividades.edit', $act) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Editar</a>
                                        @endcan

                                        @can('delete', $act)
                                            <form action="{{ route('actividades.destroy', $act) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de eliminar esta actividad?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Eliminar</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($actividades->hasPages())
                    <div class="px-6 py-3 border-t bg-gray-50">
                        {{ $actividades->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
