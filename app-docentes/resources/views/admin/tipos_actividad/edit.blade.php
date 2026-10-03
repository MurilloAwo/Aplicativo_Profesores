<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Tipo de Actividad: {{ $tipo->nombre }}</h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.tipos-actividad.update', $tipo) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="nombre" value="Nombre del Tipo de Actividad *" />
                    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $tipo->nombre)" required autofocus />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="categoria" value="Categoría Institucional *" />
                    <select id="categoria" name="categoria" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach (\App\Models\TipoActividad::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}" {{ old('categoria', $tipo->categoria) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="descripcion" value="Descripción / Instrucciones" />
                    <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('descripcion', $tipo->descripcion) }}</textarea>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="requiere_entidad_externa" value="1" {{ old('requiere_entidad_externa', $tipo->requiere_entidad_externa) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="ml-2 text-sm text-gray-700">Requiere vincular una entidad externa (empresa/institución)</span>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $tipo->activo) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="ml-2 text-sm text-gray-700">Tipo de actividad habilitado en formularios</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.tipos-actividad.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">Actualizar Tipo</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
