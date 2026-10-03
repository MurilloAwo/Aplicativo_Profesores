<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Sistema de Gestión Docente UNAL</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col justify-between">
    <header class="bg-emerald-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="font-bold tracking-wide text-lg text-emerald-100">Universidad Nacional de Colombia</span>
                <span class="text-emerald-400 text-sm hidden sm:inline">|</span>
                <span class="text-xs sm:text-sm text-emerald-200 hidden sm:inline">Departamento de Informática y Computación</span>
            </div>
            <a href="{{ url('/') }}" class="text-xs bg-emerald-800 hover:bg-emerald-700 px-3 py-1 rounded text-emerald-100 transition">
                Portal Docente
            </a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center @yield('badge_class', 'bg-red-100 text-red-600')">
                @yield('icon')
            </div>
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Error @yield('code')</span>
            <h1 class="text-2xl font-bold text-gray-900 mt-1 mb-3">@yield('title')</h1>
            <p class="text-gray-600 text-sm leading-relaxed mb-6">@yield('message')</p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="javascript:history.back()" class="inline-flex justify-center items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Volver atrás
                </a>
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="inline-flex justify-center items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    Ir al panel principal
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-gray-200 text-gray-600 py-4 text-center text-xs border-t border-gray-300">
        <p>Departamento de Informática y Computación — Universidad Nacional de Colombia Sede Manizales</p>
    </footer>
</body>
</html>
