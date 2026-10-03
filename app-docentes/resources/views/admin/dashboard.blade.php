<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de Administración del Departamento
        </h2>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Periodo Activo Banner -->
        <div class="bg-gradient-to-r from-emerald-800 to-teal-700 text-white rounded-lg p-6 shadow-md mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="bg-emerald-600 text-white text-xs px-2.5 py-1 rounded-full uppercase tracking-wider font-semibold">
                        Periodo Activo
                    </span>
                    <h3 class="text-2xl font-bold mt-2">
                        {{ $periodoActivo ? 'Semestre ' . $periodoActivo->codigo : 'No hay ningún periodo activo' }}
                    </h3>
                    <p class="text-emerald-100 text-sm mt-1">
                        @if ($periodoActivo)
                            Del {{ $periodoActivo->fecha_inicio->format('d/m/Y') }} al {{ $periodoActivo->fecha_fin->format('d/m/Y') }}
                            — {{ $periodoActivo->cerrado ? 'Estado: CERRADO (Solo lectura)' : 'Estado: ABIERTO (Permite registro)' }}
                        @else
                            Configure y active un periodo para que los docentes registren actividades.
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.periodos.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-emerald-800 rounded-md font-semibold text-sm hover:bg-emerald-50 transition shadow">
                        Gestionar Periodos &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Métricas / Contadores -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Docentes</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalDocentes }}</div>
                <div class="text-xs text-gray-600 mt-1">{{ $totalDocentesActivos }} activos</div>
                <a href="{{ route('admin.usuarios.index') }}" class="text-emerald-700 text-xs font-medium hover:underline mt-3 inline-block">Ver usuarios &rarr;</a>
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Grupos Semestre Activo</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalGruposActivos }}</div>
                <div class="text-xs text-gray-600 mt-1">{{ $totalAsignaturas }} asignaturas registradas</div>
                <a href="{{ route('admin.grupos.index') }}" class="text-emerald-700 text-xs font-medium hover:underline mt-3 inline-block">Ver grupos &rarr;</a>
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Tipos de Actividad</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalTiposActividad }}</div>
                <div class="text-xs text-gray-600 mt-1">Catálogo configurable</div>
                <a href="{{ route('admin.tipos-actividad.index') }}" class="text-emerald-700 text-xs font-medium hover:underline mt-3 inline-block">Gestionar tipos &rarr;</a>
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Criterios Acreditación</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalCriterios }}</div>
                <div class="text-xs text-gray-600 mt-1">Factores cargados</div>
                <a href="{{ route('admin.criterios.index') }}" class="text-emerald-700 text-xs font-medium hover:underline mt-3 inline-block">Gestionar criterios &rarr;</a>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h4 class="text-base font-semibold text-gray-900 mb-4">Acciones de Configuración Rápida</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.usuarios.create') }}" class="p-4 border rounded-lg hover:border-emerald-600 hover:bg-emerald-50/50 transition">
                    <div class="font-semibold text-gray-800 text-sm">+ Nuevo Docente / Usuario</div>
                    <div class="text-xs text-gray-500 mt-1">Registrar cuenta con datos de vinculación</div>
                </a>
                <a href="{{ route('admin.grupos.create') }}" class="p-4 border rounded-lg hover:border-emerald-600 hover:bg-emerald-50/50 transition">
                    <div class="font-semibold text-gray-800 text-sm">+ Nuevo Grupo</div>
                    <div class="text-xs text-gray-500 mt-1">Asignar materia y horario a un docente</div>
                </a>
                <a href="{{ route('admin.entidades.create') }}" class="p-4 border rounded-lg hover:border-emerald-600 hover:bg-emerald-50/50 transition">
                    <div class="font-semibold text-gray-800 text-sm">+ Entidad Externa</div>
                    <div class="text-xs text-gray-500 mt-1">Registrar empresa u organización aliada</div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
