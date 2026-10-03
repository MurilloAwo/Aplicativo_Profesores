<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('actividades.show', $actividad) }}" class="text-xs text-emerald-700 hover:underline">&larr; Volver al Detalle de la Actividad</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-1">
                    {{ __('Editar Actividad Docente') }}: {{ $actividad->titulo }}
                </h2>
                <p class="text-xs text-gray-500">
                    {{ $actividad->grupo?->asignatura?->nombre }} (Grupo {{ $actividad->grupo?->numero_grupo }}) — Semestre {{ $periodo?->codigo }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm mb-6 text-red-800 text-sm">
                <p class="font-bold mb-1">Por favor revise los siguientes errores:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('actividades.update', $actividad) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Sección 1: Contexto Académico -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 border-b pb-2 mb-4 uppercase tracking-wider text-emerald-800">
                    1. Contexto Académico
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Grupo / Asignatura Principal
                        </label>
                        <div class="p-2.5 bg-gray-50 rounded border border-gray-200 text-xs text-gray-800 font-semibold">
                            {{ $actividad->grupo?->asignatura?->codigo }} - Grupo {{ $actividad->grupo?->numero_grupo }}: {{ $actividad->grupo?->asignatura?->nombre }}
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">El grupo principal asignado a la actividad.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Tipo de Actividad <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo_actividad_id" required class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                            @foreach ($tipos as $t)
                                <option value="{{ $t->id }}" {{ old('tipo_actividad_id', $actividad->tipo_actividad_id) == $t->id ? 'selected' : '' }}>
                                    {{ $t->nombre }} ({{ ucfirst($t->categoria) }}) {{ $t->requiere_entidad_externa ? '[Req. Entidad]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Entidad Externa Vinculada (Opcional o según el tipo)
                        </label>
                        <select name="entidad_externa_id" class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Ninguna entidad externa</option>
                            @foreach ($entidades as $e)
                                <option value="{{ $e->id }}" {{ old('entidad_externa_id', $actividad->entidad_externa_id) == $e->id ? 'selected' : '' }}>
                                    {{ $e->nombre }} ({{ ucfirst($e->tipo) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Multigrupo adicional -->
                    @if ($grupos->count() > 1)
                        <div class="sm:col-span-2 bg-gray-50 p-3 rounded border border-gray-200">
                            <label class="block text-xs font-semibold text-gray-800 mb-1">
                                Cobertura Multigrupo en este periodo
                            </label>
                            <p class="text-[11px] text-gray-500 mb-2">
                                Marque los grupos que participaron conjuntamente en esta actividad:
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($grupos as $g)
                                    <label class="inline-flex items-center text-xs text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="grupos_adicionales[]" value="{{ $g->id }}"
                                               {{ in_array($g->id, old('grupos_adicionales', $gruposSeleccionados)) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                                        <span>{{ $g->asignatura?->codigo }} - G{{ $g->numero_grupo }}: {{ $g->asignatura?->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sección 2: Detalle de la Actividad -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 border-b pb-2 mb-4 uppercase tracking-wider text-emerald-800">
                    2. Detalle de la Actividad
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Título de la Actividad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titulo" value="{{ old('titulo', $actividad->titulo) }}" required maxlength="255"
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Descripción / Resumen <span class="text-red-500">*</span>
                        </label>
                        <textarea name="descripcion" rows="3" required
                                  class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">{{ old('descripcion', $actividad->descripcion) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Objetivo Pedagógico / de Formación</label>
                        <textarea name="objetivo" rows="2"
                                  class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">{{ old('objetivo', $actividad->objetivo) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Fecha de Inicio / Realización <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $actividad->fecha_inicio?->format('Y-m-d')) }}" required
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha de Finalización (Opcional)</label>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $actividad->fecha_fin?->format('Y-m-d')) }}"
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Duración en Horas <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.5" min="0.1" max="999" name="duracion_horas" value="{{ old('duracion_horas', $actividad->duracion_horas) }}" required
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Modalidad <span class="text-red-500">*</span>
                        </label>
                        <select name="modalidad" required class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="presencial" {{ old('modalidad', $actividad->modalidad) === 'presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="virtual" {{ old('modalidad', $actividad->modalidad) === 'virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="hibrida" {{ old('modalidad', $actividad->modalidad) === 'hibrida' ? 'selected' : '' }}>Híbrida</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Lugar / Plataforma</label>
                        <input type="text" name="lugar" value="{{ old('lugar', $actividad->lugar) }}"
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Estudiantes Participantes <span class="text-red-500">*</span>
                        </label>
                        <input type="number" min="0" name="numero_estudiantes_participantes" value="{{ old('numero_estudiantes_participantes', $actividad->numero_estudiantes_participantes) }}" required
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Sección 3: Datos de Invitado -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 border-b pb-2 mb-4 uppercase tracking-wider text-emerald-800">
                    3. Invitado / Conferencista (Si Aplica)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre Completo del Invitado</label>
                        <input type="text" name="nombre_invitado" value="{{ old('nombre_invitado', $actividad->nombre_invitado) }}"
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Cargo / Especialidad del Invitado</label>
                        <input type="text" name="cargo_invitado" value="{{ old('cargo_invitado', $actividad->cargo_invitado) }}"
                               class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Sección 4: Criterios de Acreditación -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 border-b pb-2 mb-4 uppercase tracking-wider text-emerald-800">
                    4. Alineación con Criterios de Acreditación (Opcional)
                </h3>
                @if ($criterios->isEmpty())
                    <p class="text-xs text-gray-500 italic">No hay criterios de acreditación cargados en el sistema actualmente.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 bg-gray-50 rounded border border-gray-200">
                        @foreach ($criterios as $crit)
                            <label class="inline-flex items-start text-xs text-gray-700 cursor-pointer">
                                <input type="checkbox" name="criterios[]" value="{{ $crit->id }}"
                                       {{ in_array($crit->id, old('criterios', $criteriosSeleccionados)) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mt-0.5 mr-2">
                                <div>
                                    <strong class="font-semibold text-gray-900">{{ $crit->codigo }}:</strong>
                                    <span>{{ $crit->nombre }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Sección 5: Resultados y Estado -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 border-b pb-2 mb-4 uppercase tracking-wider text-emerald-800">
                    5. Cierre y Estado del Registro
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Resultados Obtenidos / Conclusiones</label>
                        <textarea name="resultados_obtenidos" rows="2"
                                  class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">{{ old('resultados_obtenidos', $actividad->resultados_obtenidos) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Observaciones Generales</label>
                        <textarea name="observaciones" rows="2"
                                  class="w-full rounded border-gray-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">{{ old('observaciones', $actividad->observaciones) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Estado del Registro <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-6 mt-1">
                            <label class="inline-flex items-center text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="estado" value="registrada" {{ old('estado', $actividad->estado) === 'registrada' ? 'checked' : '' }}
                                       class="text-emerald-600 focus:ring-emerald-500 mr-2">
                                <span class="font-bold text-emerald-800">Registrada</span>
                                <span class="text-gray-500 text-[11px] ml-1">(Consolidable en el informe semestral)</span>
                            </label>
                            <label class="inline-flex items-center text-xs text-gray-700 cursor-pointer">
                                <input type="radio" name="estado" value="borrador" {{ old('estado', $actividad->estado) === 'borrador' ? 'checked' : '' }}
                                       class="text-amber-600 focus:ring-amber-500 mr-2">
                                <span class="font-bold text-amber-800">Borrador</span>
                                <span class="text-gray-500 text-[11px] ml-1">(Pendiente de completar o confirmar)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="{{ route('actividades.show', $actividad) }}" class="px-4 py-2 border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50 font-semibold transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-700 text-white rounded text-xs font-semibold hover:bg-emerald-800 shadow transition">
                    Actualizar Actividad
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
