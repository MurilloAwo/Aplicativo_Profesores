<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('actividades.index') }}" class="text-xs text-emerald-700 hover:underline">&larr; Volver al listado</a>
                    @if ($actividad->grupo)
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('materias.show', $actividad->grupo) }}" class="text-xs text-gray-500 hover:underline">
                            Ver materia ({{ $actividad->grupo->asignatura?->codigo }} - G{{ $actividad->grupo->numero_grupo }})
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-3 mt-1">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $actividad->titulo }}
                    </h2>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $actividad->estado === 'registrada' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ ucfirst($actividad->estado) }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $actividad)
                    <a href="{{ route('actividades.edit', $actividad) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-semibold hover:bg-blue-700 transition shadow">
                        Editar Actividad
                    </a>
                @endcan

                @can('delete', $actividad)
                    <form action="{{ route('actividades.destroy', $actividad) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta actividad?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded text-xs font-semibold hover:bg-red-700 transition shadow">
                            Eliminar
                        </button>
                    </form>
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

        @if (! $actividad->grupo?->periodoAcademico?->admiteRegistro())
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm mb-6 text-amber-800 text-xs">
                <strong>Periodo cerrado:</strong> Esta actividad pertenece a un periodo cerrado o inactivo. No se permiten modificaciones ni subida de nuevas evidencias.
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna Izquierda: Información Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ficha Contextual -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2">
                        Contexto Académico e Institucional
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-gray-500 block">Tipo de Actividad:</span>
                            <span class="font-bold text-gray-800">{{ $actividad->tipoActividad?->nombre }}</span>
                            <span class="text-[11px] text-gray-500 block">({{ ucfirst($actividad->tipoActividad?->categoria) }})</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Periodo Académico:</span>
                            <span class="font-bold text-gray-800">{{ $actividad->grupo?->periodoAcademico?->codigo }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Profesor Responsable:</span>
                            <span class="font-bold text-gray-800">{{ $actividad->grupo?->profesor?->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Grupo Principal:</span>
                            <span class="font-bold text-gray-800">
                                {{ $actividad->grupo?->asignatura?->codigo }} - Grupo {{ $actividad->grupo?->numero_grupo }}
                            </span>
                            <span class="text-[11px] text-gray-500 block">{{ $actividad->grupo?->asignatura?->nombre }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Modalidad:</span>
                            <span class="font-semibold text-gray-800 capitalize">{{ $actividad->modalidad }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Lugar / Plataforma:</span>
                            <span class="font-semibold text-gray-800">{{ $actividad->lugar ?? 'No especificado' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Fecha de Realización:</span>
                            <span class="font-bold text-gray-800">{{ $actividad->fecha_inicio->format('d/m/Y') }}</span>
                            @if ($actividad->fecha_fin)
                                <span class="text-gray-500 block text-[11px]">al {{ $actividad->fecha_fin->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-500 block">Duración:</span>
                            <span class="font-bold text-emerald-700 text-sm">{{ $actividad->duracion_horas }} horas</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Estudiantes Asistentes:</span>
                            <span class="font-bold text-gray-800 text-sm">{{ $actividad->numero_estudiantes_participantes }}</span>
                        </div>
                    </div>

                    @if ($actividad->entidadExterna)
                        <div class="mt-4 pt-4 border-t text-xs">
                            <span class="text-gray-500 block">Entidad Externa Asociada:</span>
                            <span class="font-bold text-gray-800">{{ $actividad->entidadExterna->nombre }}</span>
                            <span class="text-gray-500">({{ ucfirst($actividad->entidadExterna->tipo) }} — Contacto: {{ $actividad->entidadExterna->contacto ?? 'N/A' }})</span>
                        </div>
                    @endif

                    @if ($actividad->nombre_invitado)
                        <div class="mt-4 pt-4 border-t text-xs">
                            <span class="text-gray-500 block">Invitado / Conferencista:</span>
                            <span class="font-bold text-gray-800 text-sm">{{ $actividad->nombre_invitado }}</span>
                            @if ($actividad->cargo_invitado)
                                <span class="text-gray-600 block">{{ $actividad->cargo_invitado }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($actividad->grupos->count() > 1)
                        <div class="mt-4 pt-4 border-t text-xs">
                            <span class="text-gray-500 block font-semibold mb-1">Grupos cubiertos conjuntamente (Multigrupo):</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($actividad->grupos as $g)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-800">
                                        {{ $g->asignatura?->codigo }} (G{{ $g->numero_grupo }} - {{ $g->asignatura?->nombre }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Detalle Pedagógico -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Descripción</h4>
                        <p class="text-xs text-gray-800 whitespace-pre-line leading-relaxed">{{ $actividad->descripcion }}</p>
                    </div>

                    @if ($actividad->objetivo)
                        <div class="pt-3 border-t">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Objetivo de Formación</h4>
                            <p class="text-xs text-gray-800 whitespace-pre-line leading-relaxed">{{ $actividad->objetivo }}</p>
                        </div>
                    @endif

                    @if ($actividad->resultados_obtenidos)
                        <div class="pt-3 border-t">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Resultados Obtenidos</h4>
                            <p class="text-xs text-gray-800 whitespace-pre-line leading-relaxed">{{ $actividad->resultados_obtenidos }}</p>
                        </div>
                    @endif

                    @if ($actividad->observaciones)
                        <div class="pt-3 border-t">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Observaciones</h4>
                            <p class="text-xs text-gray-800 whitespace-pre-line leading-relaxed">{{ $actividad->observaciones }}</p>
                        </div>
                    @endif
                </div>

                <!-- Criterios de Acreditación -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                        Criterios / Factores de Acreditación Asociados
                    </h3>
                    @if ($actividad->criterios->isEmpty())
                        <p class="text-xs text-gray-500 italic">No se vincularon criterios de acreditación a esta actividad.</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($actividad->criterios as $crit)
                                <div class="p-2.5 bg-emerald-50/50 rounded border border-emerald-100 text-xs">
                                    <span class="font-bold text-emerald-800">{{ $crit->codigo }}:</span>
                                    <span class="font-semibold text-gray-800">{{ $crit->nombre }}</span>
                                    @if ($crit->descripcion)
                                        <p class="text-[11px] text-gray-600 mt-0.5">{{ $crit->descripcion }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha: Gestión de Evidencias (Privadas) -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4 border-b pb-2">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider text-emerald-800">
                            Evidencias Adjuntas
                        </h3>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-gray-100 rounded text-gray-600">
                            {{ $actividad->evidencias->count() }} archivo(s)
                        </span>
                    </div>

                    <!-- Listado de Evidencias -->
                    @if ($actividad->evidencias->isEmpty())
                        <div class="text-center py-6 text-gray-400 text-xs">
                            <svg class="mx-auto h-8 w-8 text-gray-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p>No hay evidencias subidas aún.</p>
                        </div>
                    @else
                        <div class="space-y-3 mb-6">
                            @foreach ($actividad->evidencias as $evidencia)
                                <div class="p-3 bg-gray-50 rounded border border-gray-200 flex flex-col gap-2 text-xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 mb-1">
                                                {{ $evidencia->tipo_etiqueta }}
                                            </span>
                                            <p class="font-semibold text-gray-800 truncate" title="{{ $evidencia->nombre_original }}">
                                                {{ $evidencia->nombre_original }}
                                            </p>
                                            <p class="text-[10px] text-gray-500">
                                                {{ number_format($evidencia->tamano / 1024, 1) }} KB &bull; {{ $evidencia->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-200/60">
                                        <a href="{{ route('evidencias.download', [$evidencia, 'inline' => 1]) }}" target="_blank"
                                           class="text-[11px] text-blue-600 hover:underline font-semibold">
                                            Ver
                                        </a>
                                        <a href="{{ route('evidencias.download', $evidencia) }}"
                                           class="text-[11px] text-emerald-700 hover:underline font-semibold">
                                            Descargar
                                        </a>

                                        @can('delete', $evidencia)
                                            <form action="{{ route('evidencias.destroy', $evidencia) }}" method="POST"
                                                  onsubmit="return confirm('¿Desea eliminar esta evidencia?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[11px] text-red-600 hover:underline font-semibold">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Formulario de Subida de Evidencia -->
                    @can('update', $actividad)
                        <div class="pt-4 border-t">
                            <h4 class="text-xs font-bold text-gray-800 mb-2">Subir Nueva Evidencia</h4>
                            <form action="{{ route('actividades.evidencias.store', $actividad) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tipo de Evidencia</label>
                                    <select name="tipo" required class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="foto">Foto o Registro Visual</option>
                                        <option value="acta">Acta o Minuta</option>
                                        <option value="lista_asistencia">Lista de Asistencia</option>
                                        <option value="certificado">Certificado o Constancia</option>
                                        <option value="otro">Otro Documento</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                        Archivo (JPG, PNG, PDF, DOCX, XLSX &bull; Máx. 10 MB)
                                    </label>
                                    <input type="file" name="archivo" required
                                           accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx"
                                           class="w-full text-xs text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                </div>

                                <button type="submit" class="w-full py-2 bg-emerald-700 text-white rounded text-xs font-semibold hover:bg-emerald-800 transition shadow">
                                    Subir Archivo de Evidencia
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-3 bg-gray-50 rounded text-center text-xs text-gray-500 italic">
                            No es posible subir nuevas evidencias (periodo cerrado o sin permisos de edición).
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
