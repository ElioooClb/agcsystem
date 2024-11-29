<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700 data_nav">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 menu_nav">
            <div class="flex flex_nav ">
                <!-- Logo -->
                <div class="flex items-center shrink-0 logo">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('front/images/logo.png') }}" alt="" class="logo_nav">
                    </a>
                </div>
                <!-- menu pour admin et technicien -->
                @if (Auth::user()->role_id === 1 || Auth::user()->role_id === 2)
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 nav-hiden sm:-my-px sm:ml-10 sm:flex nav_max">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Mon Tableau de Bord
                        </x-nav-link>
                        <x-nav-link :href="route('planning.index', Auth::user())">
                            Planning
                        </x-nav-link>
                        <x-nav-link :href="route('time.shows', Auth::user())">
                            Déclaration d'heures
                        </x-nav-link>
                        {{-- DEBUT - [SPECGT31] --}}
                        <button type="button" id="returnBtn" class="btn btn-primary">
                            Retour
                        </button>
                        {{-- FIN - [SPECGT31] --}}
                    </div>
            </div>
            <!-- menu pour le mode demo de la tv -->
        @elseif(Auth::user()->role_id === 3)
            <x-nav-link :href="route('planning.index', Auth::user())" style="margin-top: 15px; margin-right: 60px; margin-left: 60px; ">
                Planning
            </x-nav-link>
            @endif



            <!-- menu responsive -->
            <div class="hidden nav-hiden sm:flex sm:items-center sm:ml-6 btn_profile nav_min">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md btn_choise_nav dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                            <div>Menu</div>

                            <div class="ml-1 btn_nav_profile">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="menu_absolute">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                Mon Tableau de Bord
                            </x-nav-link>
                            <x-nav-link :href="route('planning.index', Auth::user())">
                                Planning
                            </x-nav-link>
                            <x-nav-link :href="route('time.shows', Auth::user())">
                                Déclaration d'heures
                            </x-nav-link>
                            <!-- Authentication -->

                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
            <!-- fin menu ressponsive-->



            <div class="hidden nav-hiden sm:flex sm:items-center sm:ml-6 btn_profile">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md btn_choise_nav dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1 btn_nav_profile">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="menu_profil">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- DEBUT - [SPECGT31] - Logique pour le bouton retour en arrière --}}
<script>
    document.getElementById('returnBtn').addEventListener('click', function() {
        document.referrer ? history.go(-1) : window.location.href = 'dashboard.blade.php';
    });
</script>
{{-- FIN - [SPECGT31] --}}
