<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Consolidado Departamental de Actividades Docentes') }}
                </h2>
                <p class="text-xs text-gray-500">
                    {{ config('institucion.departamento') }} — {{ config('institucion.facultad') }}
                </p>
            </div>
            @if ($periodo)
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.consolidado.pdf', ['periodo_id' => $periodo->id]) }}"
                       class="inline-flex items-center px-3.5 py-2 bg-red-700 text-white rounded-md text-xs font-semibold hover:bg-red-800 transition shadow">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar PDF
                    </a>
                    <a href="{{ route('admin.consolidado.excel', ['periodo_id' => $periodo->id]) }}"
                       class="inline-flex items-center px-3.5 py-2 bg-emerald-700 text-white rounded-md text-xs font-semibold hover:bg-emerald-800 transition shadow">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar Excel
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-6">
        <!-- Selector de Periodo -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.consolidado.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label for="periodo_id" class="text-xs font-bold text-gray-700 whitespace-nowrap">
                        Periodo Académico:
                    </label>
                    <select name="periodo_id" id="periodo_id" onchange="this.form.submit()"
                            class="rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500 min-w-[240px]">
                        @foreach ($periodos as $p)
                            <option value="{{ $p->id }}" {{ $periodo && $periodo->id === $p->id ? 'selected' : '' }}>
                                {{ $p->codigo }} {{ $p->activo ? '(Activo)' : '' }} {{ $p->cerrado ? '[Cerrado]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($periodo)
                    <div class="text-xs text-gray-500">
                        Vigencia: {{ $periodo->fecha_inicio?->format('d/m/Y') }} al {{ $periodo->fecha_fin?->format('d/m/Y') }}
                    </div>
                @endif
            </form>
        </div>

        @if (! $periodo)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center text-gray-500">
                <p class="text-base font-semibold text-gray-700">No hay periodos académicos disponibles.</p>
            </div>
        @else
            <!-- Tarjetas de KPI Globales -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Docentes</span>
                    <span class="text-xl font-black text-gray-800">{{ $totales['docentes_activos'] }}</span>
                    <span class="text-[10px] text-gray-500 block">activos</span>
                </div>

                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Grupos</span>
                    <span class="text-xl font-black text-gray-800">{{ $totales['grupos_ofertados'] }}</span>
                    <span class="text-[10px] text-gray-500 block">ofertados</span>
                </div>

                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Asignaturas</span>
                    <span class="text-xl font-black text-gray-800">{{ $totales['asignaturas_distintas'] }}</span>
                    <span class="text-[10px] text-gray-500 block">distintas</span>
                </div>

                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Actividades</span>
                    <span class="text-xl font-black text-emerald-700">{{ $totales['actividades'] }}</span>
                    <span class="text-[10px] text-gray-500 block">totales</span>
                </div>

                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Registradas</span>
                    <span class="text-xl font-black text-green-700">{{ $totales['registradas'] }}</span>
                    <span class="text-[10px] text-gray-500 block">validadas</span>
                </div>

                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Horas</span>
                    <span class="text-xl font-black text-blue-700">{{ number_format($totales['horas'], 1) }}</span>
                    <span class="text-[10px] text-gray-500 block">acumuladas</span>
                </div>

                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Impacto</span>
                    <span class="text-xl font-black text-purple-700">{{ $totales['estudiantes'] }}</span>
                    <span class="text-[10px] text-gray-500 block">asistencias</span>
                </div>

                <div class="bg-white p-3.5 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Evidencias</span>
                    <span class="text-xl font-black text-indigo-700">{{ $totales['evidencias'] }}</span>
                    <span class="text-[10px] text-gray-500 block">archivos</span>
                </div>
            </div>

            <!-- Tabla 1: Desglose por Docente -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                        1. Resumen por Docente
                    </h3>
                    <span class="text-xs text-gray-500">{{ $por_docente->count() }} docente(s) con materias</span>
                </div>

                @if ($por_docente->isEmpty())
                    <div class="p-8 text-center text-gray-400 text-xs">
                        No hay profesores con materias registradas en este periodo académico.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                <tr>
                                    <th class="px-6 py-3 text-left">Docente</th>
                                    <th class="px-6 py-3 text-left">Dedicación / Categoría</th>
                                    <th class="px-6 py-3 text-center">Materias</th>
                                    <th class="px-6 py-3 text-center">Grupos</th>
                                    <th class="px-6 py-3 text-center">Actividades</th>
                                    <th class="px-6 py-3 text-center">Horas</th>
                                    <th class="px-6 py-3 text-center">Estudiantes</th>
                                    <th class="px-6 py-3 text-center">Evidencias</th>
                                    <th class="px-6 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($por_docente as $doc)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3">
                                            <div class="font-bold text-gray-900">{{ $doc['nombre'] }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $doc['docente']->email }}</div>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">
                                            <div>{{ $doc['dedicacion'] }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $doc['categoria'] }}</div>
                                        </td>
                                        <td class="px-6 py-3 text-center font-semibold text-gray-800">{{ $doc['materias_count'] }}</td>
                                        <td class="px-6 py-3 text-center font-semibold text-gray-800">{{ $doc['grupos_count'] }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-emerald-700">
                                            {{ $doc['actividades_count'] }}
                                            <span class="text-[10px] text-gray-400 font-normal">({{ $doc['registradas_count'] }} reg)</span>
                                        </td>
                                        <td class="px-6 py-3 text-center font-bold text-blue-700">{{ number_format($doc['horas_totales'], 1) }} h</td>
                                        <td class="px-6 py-3 text-center text-gray-800">{{ $doc['estudiantes_impactados'] }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                                {{ $doc['evidencias_count'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('actividades.index', ['periodo_id' => $periodo->id, 'buscar' => $doc['nombre']]) }}"
                                               class="text-emerald-700 hover:underline font-semibold">
                                                Ver actividades
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tabla 2: Distribución por Tipo de Actividad -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                            2. Distribución por Tipo de Actividad
                        </h3>
                    </div>
                    @if ($por_tipo->isEmpty())
                        <div class="p-6 text-center text-gray-400 text-xs">Sin actividades en este periodo.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Tipo</th>
                                        <th class="px-6 py-3 text-center">Cantidad</th>
                                        <th class="px-6 py-3 text-center">Horas</th>
                                        <th class="px-6 py-3 text-center">Participantes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($por_tipo as $t)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-6 py-3 font-semibold text-gray-900">{{ $t['nombre'] }}</td>
                                            <td class="px-6 py-3 text-center font-bold text-emerald-700">{{ $t['cantidad'] }}</td>
                                            <td class="px-6 py-3 text-center font-bold text-blue-700">{{ number_format($t['horas'], 1) }} h</td>
                                            <td class="px-6 py-3 text-center text-gray-800">{{ $t['estudiantes'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Tabla 3: Distribución por Programa Curricular -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                            3. Distribución por Programa Curricular
                        </h3>
                    </div>
                    @if ($por_programa->isEmpty())
                        <div class="p-6 text-center text-gray-400 text-xs">Sin programas vinculados.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Programa</th>
                                        <th class="px-6 py-3 text-center">Grupos</th>
                                        <th class="px-6 py-3 text-center">Estudiantes</th>
                                        <th class="px-6 py-3 text-center">Actividades</th>
                                        <th class="px-6 py-3 text-center">Horas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($por_programa as $prog)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-6 py-3 font-semibold text-gray-900">{{ $prog['programa'] }}</td>
                                            <td class="px-6 py-3 text-center text-gray-700">{{ $prog['grupos_count'] }}</td>
                                            <td class="px-6 py-3 text-center text-gray-700">{{ $prog['estudiantes_matriculados'] }}</td>
                                            <td class="px-6 py-3 text-center font-bold text-emerald-700">{{ $prog['actividades_count'] }}</td>
                                            <td class="px-6 py-3 text-center font-bold text-blue-700">{{ number_format($prog['horas_acumuladas'], 1) }} h</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabla 4: Criterios de Acreditación -->
            @if ($criterios->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">
                            4. Cobertura de Criterios de Acreditación en el Departamento
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                <tr>
                                    <th class="px-6 py-3 text-left">Código</th>
                                    <th class="px-6 py-3 text-left">Criterio / Factor de Acreditación</th>
                                    <th class="px-6 py-3 text-center">Actividades Vinculadas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($criterios as $crit)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-mono font-bold text-purple-900">{{ $crit['codigo'] }}</td>
                                        <td class="px-6 py-3 text-gray-900">{{ $crit['nombre'] }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-purple-800">{{ $crit['cantidad_actividades'] }}</td>
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
