<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('materias.index') }}" class="text-xs text-emerald-700 hover:underline">&larr; Volver a Mis Materias</a>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-1">
                    {{ $grupo->asignatura?->nombre }} — Grupo {{ $grupo->numero_grupo }}
                </h2>
                <p class="text-xs text-gray-500">
                    {{ $grupo->asignatura?->programaCurricular?->nombre }} — Semestre {{ $grupo->periodoAcademico?->codigo }}
                </p>
            </div>
            <div>
                @if ($grupo->periodoAcademico?->admiteRegistro())
                    @if (Route::has('actividades.create'))
                        <a href="{{ route('actividades.create', ['grupo_id' => $grupo->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition shadow">
                            + Registrar Actividad
                        </a>
                    @endif
                @else
                    <span class="inline-flex items-center px-3 py-1.5 bg-amber-100 text-amber-800 rounded-md text-xs font-semibold">
                        Periodo Cerrado (Solo Lectura)
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm mb-6 text-green-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (! $grupo->periodoAcademico?->admiteRegistro())
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm mb-6 text-amber-800 text-sm">
                <strong>Atención:</strong> Este periodo académico se encuentra cerrado. Todas las actividades y evidencias se muestran en modo de solo lectura.
            </div>
        @endif

        <!-- Ficha técnica del grupo -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Información de la Asignatura</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 text-xs">
                <div>
                    <span class="text-gray-500 block">Código SIA:</span>
                    <span class="font-bold text-gray-800 text-sm">{{ $grupo->asignatura?->codigo }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Créditos:</span>
                    <span class="font-bold text-gray-800 text-sm">{{ $grupo->asignatura?->creditos }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Modalidad:</span>
                    <span class="font-semibold text-gray-800 capitalize">{{ $grupo->modalidad }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Estudiantes Inscritos:</span>
                    <span class="font-bold text-gray-800 text-sm">{{ $grupo->numero_estudiantes }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Horario y Aula:</span>
                    <span class="font-medium text-gray-800">{{ $grupo->horario ?? 'No asignado' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Horas Acumuladas:</span>
                    <span class="font-bold text-emerald-700 text-sm">{{ number_format($totalHoras, 1) }} hrs</span>
                </div>
            </div>
        </div>

        <!-- Listado de actividades registradas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-base">Actividades Registradas</h3>
                <span class="text-xs text-gray-500">{{ $actividades->total() }} registradas</span>
            </div>

            @if ($actividades->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <p class="text-base font-semibold text-gray-700">Aún no se han registrado actividades en esta materia</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Registre charlas, visitas técnicas, proyectos de aula, uso de herramientas u otras actividades realizadas en el semestre.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Actividad / Título</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Tipo de Actividad</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600">Fecha</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600">Duración</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600">Estudiantes</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600">Evidencias</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600">Estado</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($actividades as $act)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $act->titulo }}</div>
                                        @if ($act->entidadExterna)
                                            <div class="text-xs text-emerald-700 mt-0.5">Entidad: {{ $act->entidadExterna->nombre }}</div>
                                        @endif
                                        @if ($act->criterios->isNotEmpty())
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                Criterios: {{ $act->criterios->pluck('codigo')->join(', ') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $act->tipoActividad?->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-600 text-xs">
                                        {{ $act->fecha_inicio->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-800 font-semibold text-xs">
                                        {{ $act->duracion_horas }} h
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-800 text-xs">
                                        {{ $act->numero_estudiantes_participantes }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $act->evidencias->isNotEmpty() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $act->evidencias->count() }} adjuntas
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $act->estado === 'registrada' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($act->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 text-xs">
                                        @if (Route::has('actividades.show'))
                                            <a href="{{ route('actividades.show', $act) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold">Ver detalle</a>
                                        @endif
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
