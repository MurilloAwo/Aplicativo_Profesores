<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mis Materias y Grupos Asignados
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Departamento de Informática y Computación — Universidad Nacional de Colombia
                </p>
            </div>
            <!-- Selector de periodo -->
            @if ($periodos->count() > 1)
                <form method="GET" action="{{ route('materias.index') }}" class="flex items-center gap-2">
                    <label for="periodo_id" class="text-xs text-gray-600 font-medium">Periodo:</label>
                    <select id="periodo_id" name="periodo_id" onchange="this.form.submit()"
                            class="text-xs border-gray-300 rounded-md py-1.5 focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach ($periodos as $per)
                            <option value="{{ $per->id }}" {{ $periodoSeleccionado?->id == $per->id ? 'selected' : '' }}>
                                {{ $per->codigo }} {{ $per->activo ? '(Activo)' : '' }} {{ $per->cerrado ? '[Cerrado]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Banner del periodo seleccionado -->
        @if ($periodoSeleccionado)
            <div class="bg-white border-l-4 {{ $periodoSeleccionado->admiteRegistro() ? 'border-emerald-500' : 'border-amber-500' }} rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 text-lg">Semestre {{ $periodoSeleccionado->codigo }}</h3>
                            @if ($periodoSeleccionado->activo)
                                <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full font-semibold">Semestre Activo</span>
                            @endif
                            @if ($periodoSeleccionado->cerrado)
                                <span class="bg-amber-100 text-amber-800 text-xs px-2 py-0.5 rounded-full font-semibold">Cerrado (Solo lectura)</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Vigencia: {{ $periodoSeleccionado->fecha_inicio->format('d/m/Y') }} al {{ $periodoSeleccionado->fecha_fin->format('d/m/Y') }}
                            @if ($periodoSeleccionado->cerrado)
                                — Las actividades de este periodo no pueden modificarse.
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-6 text-sm text-gray-600">
                        <div>
                            <span class="font-bold text-gray-900 text-lg">{{ $grupos->count() }}</span>
                            <span class="text-xs text-gray-500 block">Grupos</span>
                        </div>
                        <div>
                            <span class="font-bold text-gray-900 text-lg">{{ $totalEstudiantes }}</span>
                            <span class="text-xs text-gray-500 block">Estudiantes</span>
                        </div>
                        <div>
                            <span class="font-bold text-gray-900 text-lg">{{ $totalActividades }}</span>
                            <span class="text-xs text-gray-500 block">Actividades</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-amber-800 text-sm mb-6">
                No hay ningún periodo académico configurado en el sistema. Contacte al administrador.
            </div>
        @endif

        <!-- Listado de tarjetas de materias -->
        @if ($grupos->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-12 text-center border border-gray-100">
                <div class="mx-auto w-12 h-12 text-gray-400 mb-3">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h4 class="text-base font-semibold text-gray-800">No tiene materias asignadas en este periodo</h4>
                <p class="text-sm text-gray-500 mt-1">El administrador del departamento asignará sus asignaturas y grupos correspondientes.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($grupos as $g)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                        <div class="p-5">
                            <div class="flex justify-between items-start gap-2 mb-2">
                                <span class="bg-emerald-50 text-emerald-800 text-xs px-2.5 py-0.5 rounded font-bold uppercase tracking-wider">
                                    Grupo {{ $g->numero_grupo }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $g->asignatura?->creditos }} Créditos
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 leading-snug">
                                {{ $g->asignatura?->nombre }}
                            </h3>
                            <div class="text-xs text-gray-500 mt-1">
                                Cód. SIA: {{ $g->asignatura?->codigo }} — {{ $g->asignatura?->programaCurricular?->nombre }}
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 space-y-1.5 text-xs text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-500">Modalidad:</span>
                                    <span class="capitalize">{{ $g->modalidad }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-500">Horario:</span>
                                    <span>{{ $g->horario ?? 'Por definir' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-500">Estudiantes:</span>
                                    <span>{{ $g->numero_estudiantes }} inscritos</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center text-xs">
                            <div class="font-semibold text-gray-700">
                                {{ $g->actividades_principales_count }} {{ $g->actividades_principales_count === 1 ? 'actividad' : 'actividades' }}
                            </div>
                            <a href="{{ route('materias.show', $g) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-emerald-700 text-white rounded-md font-semibold hover:bg-emerald-800 transition">
                                Ver Materia &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
