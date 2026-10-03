<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Periodo Académico: {{ $periodo->codigo }}</h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.periodos.update', $periodo) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="codigo" value="Código de Periodo *" />
                    <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full" :value="old('codigo', $periodo->codigo)" required autofocus />
                    <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="fecha_inicio" value="Fecha de Inicio *" />
                        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" :value="old('fecha_inicio', $periodo->fecha_inicio?->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="fecha_fin" value="Fecha de Fin *" />
                        <x-text-input id="fecha_fin" name="fecha_fin" type="date" class="mt-1 block w-full" :value="old('fecha_fin', $periodo->fecha_fin?->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('fecha_fin')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.periodos.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">Actualizar Periodo</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
