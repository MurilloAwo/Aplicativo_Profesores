<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Programas Curriculares</h2>
            <a href="{{ route('admin.programas.create') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm font-semibold hover:bg-emerald-800 transition">
                + Nuevo Programa
            </a>
        </div>
    </x-slot>

    @include('admin.partials.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Código</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600">Nombre del Programa</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Nivel</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600">Asignaturas</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($programas as $p)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $p->codigo }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $p->nombre }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $p->nivel === 'posgrado' ? 'bg-indigo-100 text-indigo-800' : 'bg-sky-100 text-sky-800' }}">
                                        {{ ucfirst($p->nivel) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">{{ $p->asignaturas_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.programas.edit', $p) }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs">Editar</a>
                                    @if ($p->asignaturas_count === 0)
                                        <form method="POST" action="{{ route('admin.programas.destroy', $p) }}" class="inline" onsubmit="return confirm('¿Eliminar este programa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No hay programas curriculares registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($programas->hasPages())
                <div class="px-6 py-3 border-t bg-gray-50">
                    {{ $programas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
