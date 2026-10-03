<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Grupo: {{ $grupo->nombre }}</h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.grupos.update', $grupo) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="periodo_academico_id" value="Periodo Académico *" />
                    <select id="periodo_academico_id" name="periodo_academico_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($periodos as $per)
                            <option value="{{ $per->id }}" {{ old('periodo_academico_id', $grupo->periodo_academico_id) == $per->id ? 'selected' : '' }}>
                                {{ $per->codigo }} {{ $per->activo ? '(Vigente)' : '' }} {{ $per->cerrado ? '[Cerrado]' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('periodo_academico_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="asignatura_id" value="Asignatura *" />
                    <select id="asignatura_id" name="asignatura_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($asignaturas as $asig)
                            <option value="{{ $asig->id }}" {{ old('asignatura_id', $grupo->asignatura_id) == $asig->id ? 'selected' : '' }}>
                                {{ $asig->codigo }} - {{ $asig->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('asignatura_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="profesor_id" value="Profesor Docente Asignado *" />
                    <select id="profesor_id" name="profesor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($profesores as $prof)
                            <option value="{{ $prof->id }}" {{ old('profesor_id', $grupo->profesor_id) == $prof->id ? 'selected' : '' }}>
                                {{ $prof->name }} ({{ $prof->documento }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('profesor_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="numero_grupo" value="Número de Grupo *" />
                        <x-text-input id="numero_grupo" name="numero_grupo" type="text" class="mt-1 block w-full" :value="old('numero_grupo', $grupo->numero_grupo)" required />
                        <x-input-error :messages="$errors->get('numero_grupo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="modalidad" value="Modalidad *" />
                        <select id="modalidad" name="modalidad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="presencial" {{ old('modalidad', $grupo->modalidad) === 'presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="virtual" {{ old('modalidad', $grupo->modalidad) === 'virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="hibrida" {{ old('modalidad', $grupo->modalidad) === 'hibrida' ? 'selected' : '' }}>Híbrida</option>
                        </select>
                        <x-input-error :messages="$errors->get('modalidad')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="numero_estudiantes" value="Estudiantes inscritos" />
                        <x-text-input id="numero_estudiantes" name="numero_estudiantes" type="number" min="0" class="mt-1 block w-full" :value="old('numero_estudiantes', $grupo->numero_estudiantes)" />
                        <x-input-error :messages="$errors->get('numero_estudiantes')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="horario" value="Horario y Aula" />
                    <x-text-input id="horario" name="horario" type="text" class="mt-1 block w-full" :value="old('horario', $grupo->horario)" />
                    <x-input-error :messages="$errors->get('horario')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.grupos.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">Actualizar Grupo</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
