<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Resumen Semestral de Actividades') }}
                </h2>
                <p class="text-xs text-gray-500">
                    Consolidado de materias, horas, participantes y evidencias por periodo académico
                </p>
            </div>
            @if ($periodo)
                <div class="flex items-center gap-2">
                    <a href="{{ route('resumen.pdf', ['periodo_id' => $periodo->id]) }}"
                       class="inline-flex items-center px-3.5 py-2 bg-red-700 text-white rounded-md text-xs font-semibold hover:bg-red-800 transition shadow">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar PDF
                    </a>
                    <a href="{{ route('resumen.excel', ['periodo_id' => $periodo->id]) }}"
                       class="inline-flex items-center px-3.5 py-2 bg-emerald-700 text-white rounded-md text-xs font-semibold hover:bg-emerald-800 transition shadow">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar Excel
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Selector de Periodo -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('resumen.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label for="periodo_id" class="text-xs font-bold text-gray-700 whitespace-nowrap">
                        Seleccionar Semestre:
                    </label>
                    <select name="periodo_id" id="periodo_id" onchange="this.form.submit()"
                            class="rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500 min-w-[220px]">
                        @foreach ($periodos as $p)
                            <option value="{{ $p->id }}" {{ $periodo && $periodo->id === $p->id ? 'selected' : '' }}>
                                {{ $p->codigo }} {{ $p->activo ? '(Activo)' : '' }} {{ $p->cerrado ? '[Cerrado]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($periodo && $periodo->cerrado)
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-amber-100 text-amber-800">
                        Periodo Cerrado (Solo Lectura)
                    </span>
                @endif
            </form>
        </div>

        @if (! $periodo)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center text-gray-500">
                <p class="text-base font-semibold text-gray-700">No hay periodos académicos registrados en el sistema.</p>
            </div>
        @else
            <!-- Tarjetas de KPI -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Materias</span>
                    <span class="text-2xl font-black text-gray-800">{{ $totales['materias'] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">{{ $totales['grupos'] }} grupos asignados</span>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Actividades</span>
                    <span class="text-2xl font-black text-emerald-700">{{ $totales['actividades'] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">{{ $totales['registradas'] }} registradas</span>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">En Borrador</span>
                    <span class="text-2xl font-black text-amber-600">{{ $totales['borrador'] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">Pendientes</span>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Horas Totales</span>
                    <span class="text-2xl font-black text-blue-700">{{ number_format($totales['horas'], 1) }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">horas acumuladas</span>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Participantes</span>
                    <span class="text-2xl font-black text-purple-700">{{ $totales['estudiantes'] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">asistencias registradas</span>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Evidencias</span>
                    <span class="text-2xl font-black text-indigo-700">{{ $totales['evidencias'] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">archivos adjuntos</span>
                </div>
            </div>

            <!-- Tabla 1: Asignaturas y Grupos del Semestre -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                        1. Asignaturas y Grupos Impartidos
                    </h3>
                    <span class="text-xs text-gray-500">{{ $por_materia->count() }} grupo(s)</span>
                </div>

                @if ($por_materia->isEmpty())
                    <div class="p-8 text-center text-gray-400 text-xs">
                        No tiene materias asignadas en el periodo {{ $periodo->codigo }}.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                <tr>
                                    <th class="px-6 py-3 text-left">Código SIA</th>
                                    <th class="px-6 py-3 text-left">Asignatura</th>
                                    <th class="px-6 py-3 text-center">Grupo</th>
                                    <th class="px-6 py-3 text-left">Programa</th>
                                    <th class="px-6 py-3 text-center">Estudiantes</th>
                                    <th class="px-6 py-3 text-center">Actividades</th>
                                    <th class="px-6 py-3 text-center">Horas</th>
                                    <th class="px-6 py-3 text-center">Evidencias</th>
                                    <th class="px-6 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($por_materia as $item)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-mono text-gray-600">{{ $item['codigo'] }}</td>
                                        <td class="px-6 py-3 font-bold text-gray-900">{{ $item['asignatura'] }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
                                                G{{ $item['numero_grupo'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">{{ $item['programa'] }}</td>
                                        <td class="px-6 py-3 text-center font-semibold text-gray-800">{{ $item['estudiantes_inscritos'] }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-emerald-700">{{ $item['cantidad_actividades'] }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-gray-800">{{ number_format($item['horas_acumuladas'], 1) }} h</td>
                                        <td class="px-6 py-3 text-center">{{ $item['evidencias_acumuladas'] }}</td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('materias.show', $item['grupo']) }}" class="text-emerald-700 hover:underline font-semibold">
                                                Ver materia
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Tabla 2: Distribución por Tipo de Actividad -->
            @if ($por_tipo->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                            2. Distribución por Tipo de Actividad
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                <tr>
                                    <th class="px-6 py-3 text-left">Tipo de Actividad</th>
                                    <th class="px-6 py-3 text-left">Categoría</th>
                                    <th class="px-6 py-3 text-center">Cantidad</th>
                                    <th class="px-6 py-3 text-center">Horas Totales</th>
                                    <th class="px-6 py-3 text-center">Participantes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($por_tipo as $t)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-bold text-gray-900">{{ $t['nombre'] }}</td>
                                        <td class="px-6 py-3 capitalize text-gray-600">{{ $t['categoria'] }}</td>
                                        <td class="px-6 py-3 text-center font-semibold">{{ $t['cantidad'] }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-emerald-700">{{ number_format($t['horas'], 1) }} h</td>
                                        <td class="px-6 py-3 text-center text-gray-800">{{ $t['estudiantes'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Tabla 3: Lista de Actividades del Periodo -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                        3. Actividades del Semestre
                    </h3>
                    <span class="text-xs text-gray-500">{{ $actividades->count() }} actividad(es)</span>
                </div>

                @if ($actividades->isEmpty())
                    <div class="p-8 text-center text-gray-400 text-xs">
                        No se registran actividades para este periodo académico.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                <tr>
                                    <th class="px-6 py-3 text-left">Actividad</th>
                                    <th class="px-6 py-3 text-left">Materia / Grupo</th>
                                    <th class="px-6 py-3 text-left">Tipo</th>
                                    <th class="px-6 py-3 text-center">Fecha</th>
                                    <th class="px-6 py-3 text-center">Horas</th>
                                    <th class="px-6 py-3 text-center">Partic.</th>
                                    <th class="px-6 py-3 text-center">Evidencias</th>
                                    <th class="px-6 py-3 text-center">Estado</th>
                                    <th class="px-6 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($actividades as $act)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3">
                                            <a href="{{ route('actividades.show', $act) }}" class="font-bold text-gray-900 hover:text-emerald-700 transition">
                                                {{ $act->titulo }}
                                            </a>
                                            @if ($act->nombre_invitado)
                                                <div class="text-[11px] text-gray-500">Invitado: {{ $act->nombre_invitado }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">
                                            {{ $act->grupo?->asignatura?->codigo }} (G{{ $act->grupo?->numero_grupo }})
                                            @if ($act->grupos->count() > 1)
                                                <span class="text-[10px] text-indigo-600 block">[+{{ $act->grupos->count() - 1 }} multigrupo]</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-gray-800">{{ $act->tipoActividad?->nombre }}</td>
                                        <td class="px-6 py-3 text-center text-gray-600">{{ $act->fecha_inicio?->format('d/m/Y') }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-gray-800">{{ $act->duracion_horas }} h</td>
                                        <td class="px-6 py-3 text-center text-gray-800">{{ $act->numero_estudiantes_participantes }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium {{ $act->evidencias->isNotEmpty() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $act->evidencias->count() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $act->estado === 'registrada' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ ucfirst($act->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('actividades.show', $act) }}" class="text-emerald-700 hover:underline font-semibold">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Tabla 4: Criterios de Acreditación -->
            @if ($criterios->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                            4. Tributación a Criterios de Acreditación
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                <tr>
                                    <th class="px-6 py-3 text-left">Código</th>
                                    <th class="px-6 py-3 text-left">Criterio / Factor</th>
                                    <th class="px-6 py-3 text-center">Actividades Vinculadas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($criterios as $crit)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-bold text-emerald-800 font-mono">{{ $crit['codigo'] }}</td>
                                        <td class="px-6 py-3 font-medium text-gray-900">{{ $crit['nombre'] }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-emerald-700">{{ $crit['cantidad_actividades'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
