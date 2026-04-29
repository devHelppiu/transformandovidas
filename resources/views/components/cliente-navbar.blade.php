<header class="bg-white border-b border-[#e8ebff] sticky top-0 z-40">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        {{-- Logo --}}
        <a href="{{ route('cliente.dashboard') }}" class="flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Transformando Vidas" class="h-[58px] w-auto">
        </a>

        {{-- Pill usuario con dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <div class="border-2 border-[#e8ebff] rounded-lg flex items-center">
                <button @click="open = !open"
                        @click.outside="open = false"
                        class="flex items-center gap-2.5 px-4 py-2.5 focus:outline-none">
                    {{-- Ícono usuario --}}
                    <svg class="w-4 h-4 text-tv-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-montserrat font-medium text-base text-black whitespace-nowrap">
                        {{ Auth::user()->name }}
                    </span>
                    {{-- Chevron --}}
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Dropdown --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-[#e8ebff] py-1 z-50"
                 style="display: none;">
                <a href="{{ route('profile.edit') }}"
                   class="block px-4 py-2 font-urbanist text-sm text-gray-700 hover:bg-tv-bg transition-colors">
                    Perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 font-urbanist text-sm text-gray-700 hover:bg-tv-bg transition-colors">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
