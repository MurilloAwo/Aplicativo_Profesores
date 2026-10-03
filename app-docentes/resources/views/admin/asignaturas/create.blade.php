<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Asignatura</h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.asignaturas.store') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="programa_curricular_id" value="Programa Curricular *" />
                    <select id="programa_curricular_id" name="programa_curricular_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Seleccione un programa...</option>
                        @foreach ($programas as $p)
                            <option value="{{ $p->id }}" {{ old('programa_curricular_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->codigo }} - {{ $p->nombre }} ({{ ucfirst($p->nivel) }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('programa_curricular_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="codigo" value="Código SIA / Asignatura *" />
                        <x-text-input id="codigo" name="codigo" type="text" placeholder="Ej. 2016699" class="mt-1 block w-full" :value="old('codigo')" required />
                        <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="creditos" value="Créditos Académicos *" />
                        <x-text-input id="creditos" name="creditos" type="number" min="1" max="10" class="mt-1 block w-full" :value="old('creditos', 3)" required />
                        <x-input-error :messages="$errors->get('creditos')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="nombre" value="Nombre de la Asignatura *" />
                    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="tipologia" value="Tipología" />
                    <x-text-input id="tipologia" name="tipologia" type="text" placeholder="Ej. Disciplinar Obligatoria, Optativa, Fundamental" class="mt-1 block w-full" :value="old('tipologia')" />
                    <x-input-error :messages="$errors->get('tipologia')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.asignaturas.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">Guardar Asignatura</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
