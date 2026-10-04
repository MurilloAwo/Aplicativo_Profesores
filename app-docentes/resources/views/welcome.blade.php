<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Actividades Docentes — Depto. Informática y Computación | UNAL</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- Top institutional banner -->
    <div class="bg-emerald-950 text-emerald-100 text-xs py-2 px-4 sm:px-8 border-b border-emerald-900">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-1">
            <span class="font-medium tracking-wide">Universidad Nacional de Colombia — Sede Manizales</span>
            <span class="text-emerald-300">Departamento de Informática y Computación</span>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-800 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-md shadow-emerald-800/20">
                    UNAL
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">Actividades Docentes</h1>
                    <p class="text-xs text-slate-500 font-medium">Sistema Departamental de Registro y Acreditación</p>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <span>Ir al Panel Principal</span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white text-sm font-semibold rounded-lg shadow-md shadow-emerald-700/20 transition-all hover:scale-102">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Iniciar Sesión</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-b from-emerald-50/70 via-white to-slate-50 py-16 sm:py-20 border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 mb-4">
                        <span class="w-2 h-2 mr-2 bg-emerald-600 rounded-full animate-pulse"></span>
                        Gestión Curricular y Calidad Académica
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Seguimiento y Registro de <span class="text-emerald-700">Actividades Docentes</span>
                    </h2>
                    <p class="mt-5 text-base sm:text-lg text-slate-600 leading-relaxed">
                        Plataforma unificada para profesores y directivos del Departamento de Informática y Computación. Administre asignaturas, documente evidencias pedagógicas y genere informes semestrales de acreditación.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-xl shadow-lg shadow-emerald-700/25 transition">
                                Continuar a mi Panel
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-700/25 transition transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Ingresar al Sistema
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Test Credentials Card (Visible during development/testing) -->
                <div class="mt-14 max-w-2xl mx-auto bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
                    <div class="bg-emerald-800 text-white px-5 py-3 flex items-center justify-between text-sm font-semibold">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Cuentas de Acceso para Pruebas</span>
                        </div>
                        <span class="text-xs bg-emerald-900/60 px-2 py-0.5 rounded text-emerald-200">Ambiente Local</span>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <!-- Admin Card -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 hover:border-emerald-300 transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-slate-800">Administrador</span>
                                <span class="text-[11px] font-semibold bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Admin</span>
                            </div>
                            <div class="space-y-1 font-mono text-xs text-slate-600">
                                <p><span class="text-slate-400 font-sans">Email:</span> <strong class="text-slate-800">admin@unal.edu.co</strong></p>
                                <p><span class="text-slate-400 font-sans">Clave:</span> <strong class="text-slate-800">password123</strong></p>
                            </div>
                            <p class="mt-3 text-xs text-slate-500 font-sans">Control departamental, periodos, catálogos y consolidados.</p>
                        </div>

                        <!-- Teacher Card -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 hover:border-emerald-300 transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-slate-800">Docente</span>
                                <span class="text-[11px] font-semibold bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Profesor</span>
                            </div>
                            <div class="space-y-1 font-mono text-xs text-slate-600">
                                <p><span class="text-slate-400 font-sans">Email:</span> <strong class="text-slate-800">docente1@unal.edu.co</strong></p>
                                <p><span class="text-slate-400 font-sans">Clave:</span> <strong class="text-slate-800">password123</strong></p>
                            </div>
                            <p class="mt-3 text-xs text-slate-500 font-sans">Materias activas, registro de actividades y resumen semestral.</p>
                        </div>
                    </div>
                    <div class="bg-slate-50/60 px-5 py-2.5 border-t border-slate-100 text-center">
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 underline">
                            Ir al formulario de inicio de sesión &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Highlights -->
        <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900">Módulos del Sistema</h3>
                <p class="mt-2 text-sm sm:text-base text-slate-500">Diseñado específicamente para los lineamientos y necesidades del departamento académico.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1">Mis Materias y Grupos</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Visualización de asignaturas y grupos asignados en el periodo académico vigente, con datos de estudiantes y horas.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1">Actividades y Evidencias</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Registro de actividades multigrupo con subida segura de evidencias en PDF, Office o imágenes, con control de acceso.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1">Resumen y Reportes</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Cálculo automático del resumen semestral con descarga en PDF institucional y Excel en múltiples hojas.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1">Consolidado y Calidad</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Supervisión integral de actividades, vinculación a criterios de acreditación y consolidados departamentales.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-2">
            <p class="font-semibold text-slate-700">Universidad Nacional de Colombia · Departamento de Informática y Computación</p>
            <p>Sistema de Gestión de Actividades Docentes — Laravel 10 & Tailwind CSS</p>
        </div>
    </footer>

</body>
</html>
