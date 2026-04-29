<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Transformando Vidas" class="h-10 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('admin.sorteos.index')" :active="request()->routeIs('admin.sorteos.*')">
                                Sorteos
                            </x-nav-link>
                            <x-nav-link :href="route('admin.coordinadores.index')" :active="request()->routeIs('admin.coordinadores.*')">
                                Coordinadores
                            </x-nav-link>
                            <x-nav-link :href="route('admin.comerciales.index')" :active="request()->routeIs('admin.comerciales.*')">
                                Comerciales
                            </x-nav-link>
                            <x-nav-link :href="route('admin.pagos.index')" :active="request()->routeIs('admin.pagos.*')">
                                Pagos
                            </x-nav-link>
                            <x-nav-link :href="route('admin.comisiones.config')" :active="request()->routeIs('admin.comisiones.*')">
                                Comisiones
                            </x-nav-link>
                            <x-nav-link :href="route('admin.reportes.index')" :active="request()->routeIs('admin.reportes.*')">
                                Reportes
                            </x-nav-link>
                        @elseif(auth()->user()->isCoordinador())
                            <x-nav-link :href="route('coordinador.dashboard')" :active="request()->routeIs('coordinador.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('coordinador.lideres.index')" :active="request()->routeIs('coordinador.lideres.*')">
                                Mis Líderes
                            </x-nav-link>
                            <x-nav-link :href="route('coordinador.comisiones.index')" :active="request()->routeIs('coordinador.comisiones.*')">
                                Comisiones
                            </x-nav-link>
                            <x-nav-link :href="route('coordinador.reportes.index')" :active="request()->routeIs('coordinador.reportes.*')">
                                Reportes
                            </x-nav-link>
                        @elseif(auth()->user()->isLider())
                            <x-nav-link :href="route('lider.dashboard')" :active="request()->routeIs('lider.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('lider.comerciales.index')" :active="request()->routeIs('lider.comerciales.*')">
                                Mis Comerciales
                            </x-nav-link>
                            <x-nav-link :href="route('lider.comisiones.index')" :active="request()->routeIs('lider.comisiones.*')">
                                Comisiones
                            </x-nav-link>
                            <x-nav-link :href="route('lider.reportes.index')" :active="request()->routeIs('lider.reportes.*')">
                                Reportes
                            </x-nav-link>
                        @elseif(auth()->user()->isComercial())
                            <x-nav-link :href="route('comercial.dashboard')" :active="request()->routeIs('comercial.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('comercial.comisiones.index')" :active="request()->routeIs('comercial.comisiones.*')">
                                Comisiones
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('cliente.dashboard')" :active="request()->routeIs('cliente.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('cliente.tickets.index')" :active="request()->routeIs('cliente.tickets.*')">
                                Mis Tickets
                            </x-nav-link>
                            <x-nav-link :href="route('cliente.historial')" :active="request()->routeIs('cliente.historial')">
                                Historial
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
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
                            Perfil
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <a href="{{ route('consulta.tickets') }}" class="inline-flex items-center gap-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-md font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Mis Tickets
                </a>
                @endauth
            </div>

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
            @auth
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.sorteos.index')">Sorteos</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.coordinadores.index')">Coordinadores</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.comerciales.index')">Comerciales</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.pagos.index')">Pagos</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.comisiones.config')">Comisiones</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reportes.index')">Reportes</x-responsive-nav-link>
                @elseif(auth()->user()->isCoordinador())
                    <x-responsive-nav-link :href="route('coordinador.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('coordinador.lideres.index')">Mis Líderes</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('coordinador.comisiones.index')">Comisiones</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('coordinador.reportes.index')">Reportes</x-responsive-nav-link>
                @elseif(auth()->user()->isLider())
                    <x-responsive-nav-link :href="route('lider.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('lider.comerciales.index')">Mis Comerciales</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('lider.comisiones.index')">Comisiones</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('lider.reportes.index')">Reportes</x-responsive-nav-link>
                @elseif(auth()->user()->isComercial())
                    <x-responsive-nav-link :href="route('comercial.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('comercial.comisiones.index')">Comisiones</x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('cliente.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cliente.tickets.index')">Mis Tickets</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cliente.historial')">Historial</x-responsive-nav-link>
                @endif
            @endauth
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar Sesión
                    </x-responsive-nav-link>
                </form>
            </div>
            @else
            <div class="px-4 py-2 space-y-2">
                <x-responsive-nav-link :href="route('consulta.tickets')">Mis Tickets</x-responsive-nav-link>
            </div>
            @endauth
        </div>
    </div>
</nav>
