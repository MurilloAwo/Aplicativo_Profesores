<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Entidad Externa</h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('admin.entidades.store') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="nombre" value="Razón Social / Nombre de la Organización *" />
                    <x-text-input id="nombre" name="nombre" type="text" placeholder="Ej. IBM Colombia S.A.S." class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nit" value="NIT (Opcional)" />
                        <x-text-input id="nit" name="nit" type="text" placeholder="Ej. 860000000-1" class="mt-1 block w-full" :value="old('nit')" />
                        <x-input-error :messages="$errors->get('nit')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="sector" value="Sector Económico / Tipo" />
                        <x-text-input id="sector" name="sector" type="text" placeholder="Ej. Tecnología, Financiero, Público" class="mt-1 block w-full" :value="old('sector')" />
                        <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="ciudad" value="Ciudad" />
                        <x-text-input id="ciudad" name="ciudad" type="text" placeholder="Ej. Bogotá D.C." class="mt-1 block w-full" :value="old('ciudad')" />
                        <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="contacto" value="Persona o Canal de Contacto" />
                        <x-text-input id="contacto" name="contacto" type="text" placeholder="Ej. Ing. Juan Pérez - j.perez@ibm.com" class="mt-1 block w-full" :value="old('contacto')" />
                        <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.entidades.index') }}" class="px-4 py-2 border rounded-md text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">Guardar Entidad</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
