<div class="bg-white border-b border-gray-200 mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-4 overflow-x-auto py-2 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Resumen
            </a>
            <a href="{{ route('admin.usuarios.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.usuarios.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Usuarios
            </a>
            <a href="{{ route('admin.periodos.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.periodos.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Periodos
            </a>
            <a href="{{ route('admin.programas.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.programas.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Programas
            </a>
            <a href="{{ route('admin.asignaturas.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.asignaturas.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Asignaturas
            </a>
            <a href="{{ route('admin.grupos.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.grupos.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Grupos
            </a>
            <a href="{{ route('admin.tipos-actividad.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.tipos-actividad.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Tipos de Actividad
            </a>
            <a href="{{ route('admin.entidades.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.entidades.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Entidades Externas
            </a>
            <a href="{{ route('admin.criterios.index') }}"
               class="px-3 py-1.5 rounded-md {{ request()->routeIs('admin.criterios.*') ? 'bg-emerald-700 text-white font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                Criterios Acreditación
            </a>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
            <div class="flex">
                <div class="text-green-800 text-sm font-medium">{{ session('success') }}</div>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <div class="flex">
                <div class="text-red-800 text-sm font-medium">{{ session('error') }}</div>
            </div>
        </div>
    </div>
@endif
