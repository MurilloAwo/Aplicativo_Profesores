<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold text-lg text-emerald-700 tracking-tight flex items-center gap-2">
                        <span class="bg-emerald-700 text-white px-2 py-1 rounded text-sm font-semibold">UNAL</span>
                        <span>Actividades Docentes</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Panel Principal') }}
                    </x-nav-link>

                    @if (Auth::user()->esProfesor())
                        @if (Route::has('materias.index'))
                            <x-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                                {{ __('Mis Materias') }}
                            </x-nav-link>
                        @endif
                        @if (Route::has('actividades.index'))
                            <x-nav-link :href="route('actividades.index')" :active="request()->routeIs('actividades.*')">
                                {{ __('Actividades') }}
                            </x-nav-link>
                        @endif
                        @if (Route::has('resumen.index'))
                            <x-nav-link :href="route('resumen.index')" :active="request()->routeIs('resumen.*')">
                                {{ __('Resumen Semestral') }}
                            </x-nav-link>
                        @endif
                    @endif

                    @if (Auth::user()->esAdmin())
                        @if (Route::has('admin.usuarios.index'))
                            <x-nav-link :href="route('admin.usuarios.index')" :active="request()->routeIs('admin.*')">
                                {{ __('Administración') }}
                            </x-nav-link>
                        @endif
                        @if (Route::has('admin.consolidado.index'))
                            <x-nav-link :href="route('admin.consolidado.index')" :active="request()->routeIs('admin.consolidado.*')">
                                {{ __('Consolidado') }}
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-2 {{ Auth::user()->esAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ Auth::user()->esAdmin() ? 'Administrador' : 'Profesor' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mi Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Panel Principal') }}
            </x-responsive-nav-link>

            @if (Auth::user()->esProfesor())
                @if (Route::has('materias.index'))
                    <x-responsive-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                        {{ __('Mis Materias') }}
                    </x-responsive-nav-link>
                @endif
                @if (Route::has('actividades.index'))
                    <x-responsive-nav-link :href="route('actividades.index')" :active="request()->routeIs('actividades.*')">
                        {{ __('Actividades') }}
                    </x-responsive-nav-link>
                @endif
                @if (Route::has('resumen.index'))
                    <x-responsive-nav-link :href="route('resumen.index')" :active="request()->routeIs('resumen.*')">
                        {{ __('Resumen Semestral') }}
                    </x-responsive-nav-link>
                @endif
            @endif

            @if (Auth::user()->esAdmin())
                @if (Route::has('admin.usuarios.index'))
                    <x-responsive-nav-link :href="route('admin.usuarios.index')" :active="request()->routeIs('admin.*')">
                        {{ __('Administración') }}
                    </x-responsive-nav-link>
                @endif
                @if (Route::has('admin.consolidado.index'))
                    <x-responsive-nav-link :href="route('admin.consolidado.index')" :active="request()->routeIs('admin.consolidado.*')">
                        {{ __('Consolidado') }}
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 flex items-center gap-2">
                    <span>{{ Auth::user()->name }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ Auth::user()->esAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ Auth::user()->esAdmin() ? 'Admin' : 'Profesor' }}
                    </span>
                </div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Mi Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
