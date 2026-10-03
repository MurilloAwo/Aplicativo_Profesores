<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Programa Curricular: {{ $programa->nombre }}</h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.programas.update', $programa) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="codigo" value="Código del Programa *" />
                    <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full" :value="old('codigo', $programa->codigo)" required autofocus />
                    <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nombre" value="Nombre del Programa *" />
                    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $programa->nombre)" required />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nivel" value="Nivel Académico *" />
                    <select id="nivel" name="nivel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="pregrado" {{ old('nivel', $programa->nivel) === 'pregrado' ? 'selected' : '' }}>Pregrado</option>
                        <option value="posgrado" {{ old('nivel', $programa->nivel) === 'posgrado' ? 'selected' : '' }}>Posgrado</option>
                    </select>
                    <x-input-error :messages="$errors->get('nivel')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.programas.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">Actualizar Programa</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
