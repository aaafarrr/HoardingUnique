<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hoarding Unique') - {{ __("World's Most Unique Items Gallery") }}</title>

    <!-- Theme Initial Detection -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts & Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome & Leaflet Map CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .dark .glass {
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glow-gold {
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.3);
        }

        .glow-purple {
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);
        }

        .glow-red {
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }
    </style>
    @yield('styles')
</head>

<body
    class="flex flex-col min-h-full font-sans antialiased bg-slate-55 text-slate-800 dark:bg-slate-950 dark:text-slate-100 selection:bg-indigo-500 selection:text-white transition-colors duration-150">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 glass border-b border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('items.index') }}" class="flex items-center gap-2 group">
                        <div
                            class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-cube text-white text-lg"></i>
                        </div>
                        <span
                            class="hidden sm:inline text-xl font-bold tracking-tight bg-gradient-to-r from-slate-950 via-slate-800 to-indigo-650 dark:from-white dark:via-slate-100 dark:to-indigo-450 bg-clip-text text-transparent">
                            Hoarding Unique
                        </span>
                    </a>
                </div>

                <!-- Nav Links (Visible to Guest and Auth) -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('items.index') }}"
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('items.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 hover:text-slate-900 dark:text-slate-355 dark:hover:text-white' }}">
                        <i class="fa-solid fa-compass mr-1"></i> {{ __('Feed') }}
                    </a>
                    <a href="{{ route('search') }}"
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('search') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 hover:text-slate-900 dark:text-slate-355 dark:hover:text-white' }}">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> {{ __('Search') }}
                    </a>
                    <a href="{{ route('map') }}"
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('map') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 hover:text-slate-900 dark:text-slate-355 dark:hover:text-white' }}">
                        <i class="fa-solid fa-map-location-dot mr-1"></i> {{ __('Item Map') }}
                    </a>
                    <a href="{{ route('leaderboard') }}"
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('leaderboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-600 hover:text-slate-900 dark:text-slate-355 dark:hover:text-white' }}">
                        <i class="fa-solid fa-trophy mr-1"></i> {{ __('Leaderboard') }}
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-3">

                    <!-- Language Switcher -->
                    <div class="relative group">
                        <button
                            class="w-9 h-9 sm:w-auto sm:px-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-355 dark:hover:text-white transition-all flex items-center justify-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-globe text-slate-450 dark:text-slate-500"></i>
                            <span class="hidden sm:inline uppercase">{{ App::getLocale() }}</span>
                        </button>
                        <div
                            class="absolute right-0 mt-2 w-32 rounded-xl glass border border-slate-200 dark:border-slate-800 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-1 z-50">
                            <a href="{{ route('lang.switch', 'en') }}"
                                class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-750 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white {{ App::getLocale() === 'en' ? 'font-bold text-indigo-600 dark:text-indigo-400' : '' }}">
                                <span>English</span>
                                @if(App::getLocale() === 'en') <i class="fa-solid fa-check text-[10px]"></i> @endif
                            </a>
                            <a href="{{ route('lang.switch', 'id') }}"
                                class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-750 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white {{ App::getLocale() === 'id' ? 'font-bold text-indigo-600 dark:text-indigo-400' : '' }}">
                                <span>Indonesia</span>
                                @if(App::getLocale() === 'id') <i class="fa-solid fa-check text-[10px]"></i> @endif
                            </a>
                        </div>
                    </div>

                    <!-- Theme Toggle Button -->
                    <button onclick="toggleTheme()"
                        class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-100 dark:border-slate-800 dark:hover:bg-slate-900 text-slate-600 hover:text-slate-900 dark:text-slate-355 dark:hover:text-white transition-all flex items-center justify-center cursor-pointer"
                        aria-label="Toggle Theme">
                        <i id="themeToggleIcon" class="fa-solid fa-moon text-base"></i>
                    </button>

                    @auth
                        <!-- Upload Button -->
                        <a href="{{ route('items.create') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-2 sm:px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/20 transition-all hover:-translate-y-0.5">
                            <i class="fa-solid fa-plus text-xs"></i> <span
                                class="hidden sm:inline">{{ __('Upload Item') }}</span>
                        </a>

                        <!-- User Menu -->
                        <div class="relative group">
                            <button class="flex items-center gap-1.5 focus:outline-none cursor-pointer">
                                @if(Auth::user()->avatar)
                                    <img src="{{ str_starts_with(Auth::user()->avatar, 'http') ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}"
                                        alt="{{ Auth::user()->name }}"
                                        class="w-9 h-9 rounded-xl object-cover ring-2 ring-indigo-500/30">
                                @else
                                    <div
                                        class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300 font-bold ring-2 ring-indigo-500/30">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                class="absolute right-0 mt-2 w-48 rounded-xl glass border border-slate-200 dark:border-slate-800 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right transform scale-95 group-hover:scale-100 py-1 z-50">
                                <div class="px-4 py-2 border-b border-slate-200/80 dark:border-slate-800/80">
                                    <p class="text-xs text-slate-450 dark:text-slate-400">{{ __('Total Points') }}</p>
                                    <p class="text-sm font-bold text-amber-500 dark:text-amber-400"><i
                                            class="fa-solid fa-star mr-1"></i>{{ Auth::user()->points }} Pts</p>
                                </div>
                                <a href="{{ route('profile.me') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-750 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    <i class="fa-solid fa-user text-xs w-4"></i> {{ __('My Profile') }}
                                </a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-rose-500 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-rose-600 dark:hover:text-rose-350 transition-colors text-left cursor-pointer">
                                        <i class="fa-solid fa-sign-out text-xs w-4"></i> {{ __('Logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('login') }}"
                                class="text-xs sm:text-sm font-semibold text-slate-650 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white transition-colors px-3 py-2">
                                {{ __('Login') }}
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-505 text-white text-xs sm:text-sm font-semibold rounded-xl transition-all shadow shadow-indigo-600/10">
                                {{ __('Register') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Nav Bar (Bottom) -->
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 z-50 glass border-t border-slate-200 dark:border-slate-800 flex justify-around py-3 px-2">
        <a href="{{ route('items.index') }}"
            class="flex flex-col items-center gap-0.5 {{ request()->routeIs('items.index') ? 'text-indigo-650 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <i class="fa-solid fa-compass text-lg"></i>
            <span class="text-[10px]">{{ __('Feed') }}</span>
        </a>
        <a href="{{ route('search') }}"
            class="flex flex-col items-center gap-0.5 {{ request()->routeIs('search') ? 'text-indigo-650 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <i class="fa-solid fa-magnifying-glass text-lg"></i>
            <span class="text-[10px]">{{ __('Search') }}</span>
        </a>
        <a href="{{ route('map') }}"
            class="flex flex-col items-center gap-0.5 {{ request()->routeIs('map') ? 'text-indigo-650 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <i class="fa-solid fa-map-location-dot text-lg"></i>
            <span class="text-[10px]">{{ __('Item Map') }}</span>
        </a>
        <a href="{{ route('leaderboard') }}"
            class="flex flex-col items-center gap-0.5 {{ request()->routeIs('leaderboard') ? 'text-indigo-650 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <i class="fa-solid fa-trophy text-lg"></i>
            <span class="text-[10px]">{{ __('Leaderboard') }}</span>
        </a>
        <a href="{{ Auth::check() ? route('profile.me') : route('login') }}"
            class="flex flex-col items-center gap-0.5 {{ request()->routeIs('profile.me') || request()->routeIs('profile.show') ? 'text-indigo-650 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <i class="fa-solid fa-user text-lg"></i>
            <span class="text-[10px]">{{ Auth::check() ? __('My Profile') : __('Login') }}</span>
        </a>
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24 md:pb-8">
        <!-- Toast Alerts -->
        @if(session('success'))
            <div
                class="mb-6 p-4 rounded-xl glass border-emerald-500/20 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 flex items-center gap-3 animate-fade-in shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div
                class="mb-6 p-4 rounded-xl glass border-amber-500/20 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 flex items-center gap-3 animate-fade-in shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg"></i>
                <span class="text-sm font-medium">{{ session('warning') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div
                class="mb-6 p-4 rounded-xl glass border-rose-500/20 bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300 flex items-center gap-3 animate-fade-in shadow-sm">
                <i class="fa-solid fa-circle-xmark text-rose-500 text-lg"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer
        class="mt-auto border-t border-slate-200 dark:border-slate-900 bg-white/40 dark:bg-slate-955/60 py-6 pb-24 md:pb-6 text-center text-xs text-slate-500">
        <p>&copy; 2026 Hoarding Unique. {{ __('Created with dedication.') }}</p>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Theme Toggle Switch Script -->
    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeIcon();
            window.dispatchEvent(new Event('theme-changed'));
        }

        function updateThemeIcon() {
            const icon = document.getElementById('themeToggleIcon');
            if (!icon) return;
            if (document.documentElement.classList.contains('dark')) {
                icon.className = 'fa-solid fa-sun text-base';
            } else {
                icon.className = 'fa-solid fa-moon text-base';
            }
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcon);
    </script>
    @yield('scripts')
</body>

</html>