<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — Mringis Photobox</title>
    <meta name="description" content="Panel Admin Mringis Photobox">
    <link rel="icon" type="image/png" href="{{ asset('img/icon/icon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 border-2 border-transparent rounded-xl text-sm font-semibold transition-all duration-200 text-retro-text/80; }
        .sidebar-link:hover { @apply bg-retro-bg border-retro-text text-retro-text; }
        .sidebar-link.active { @apply bg-retro-accent1 text-white border-retro-text shadow-[4px_4px_0px_0px_#202020]; }
        .sidebar-link.active svg { @apply text-white; }
    </style>
</head>
<body class="h-full bg-retro-bg text-retro-text selection:bg-retro-accent1 selection:text-white">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
    <!-- Sidebar -->
    <aside class="flex-shrink-0 bg-white border-retro-text flex flex-col transition-all duration-300 ease-in-out overflow-hidden"
           :class="sidebarOpen ? 'w-64 border-r-4' : 'w-0 border-r-0'">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-6 border-b-4 border-retro-text flex-shrink-0" :class="sidebarOpen ? '' : 'justify-center px-4'">
            <img src="{{ asset('img/icon/icon.png') }}" class="w-10 h-10 object-contain rounded-xl bg-retro-bg border-2 border-retro-text p-1 flex-shrink-0" alt="Mringis Logo">
            <div x-show="sidebarOpen" class="transition-all duration-300">
                <h1 class="text-retro-text font-black text-lg leading-none">Mringis</h1>
                <p class="text-retro-text/60 text-xs mt-1">Photobox System</p>
            </div>
        </div>

        <!-- Admin Info -->
        <div class="px-6 py-4 border-b-4 border-retro-text bg-retro-bg/40 flex-shrink-0" :class="sidebarOpen ? '' : 'px-4 justify-center'">
            <div class="flex items-center gap-3" :class="sidebarOpen ? '' : 'justify-center'">
                <div class="w-9 h-9 bg-retro-accent2 border-2 border-retro-text rounded-full flex items-center justify-center text-sm font-bold text-white shadow-[2px_2px_0px_0px_#202020] flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div x-show="sidebarOpen" class="transition-all duration-300 truncate">
                    <p class="text-sm font-bold text-retro-text truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-retro-text/60 font-semibold">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 mb-4 font-bold border-2 border-[#202020] rounded text-[#202020] hover:bg-[#2a9d8f] hover:text-white transition-all shadow-[2px_2px_0px_#202020] duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-[#ee4266] text-white' : 'bg-[#fffdf0]' }}"
               :class="sidebarOpen ? 'justify-start' : 'justify-center px-2'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" class="transition-all duration-300">Dashboard</span>
            </a>

            <a href="{{ route('admin.frames.index') }}"
               class="flex items-center gap-3 px-4 py-3 mb-4 font-bold border-2 border-[#202020] rounded text-[#202020] hover:bg-[#2a9d8f] hover:text-white transition-all shadow-[2px_2px_0px_#202020] duration-300 {{ request()->routeIs('admin.frames*') ? 'bg-[#ee4266] text-white' : 'bg-[#fffdf0]' }}"
               :class="sidebarOpen ? 'justify-start' : 'justify-center px-2'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span x-show="sidebarOpen" class="transition-all duration-300">Manajemen Frame</span>
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-3 px-4 py-3 mb-4 font-bold border-2 border-[#202020] rounded text-[#202020] hover:bg-[#2a9d8f] hover:text-white transition-all shadow-[2px_2px_0px_#202020] duration-300 {{ request()->routeIs('admin.reports*') ? 'bg-[#ee4266] text-white' : 'bg-[#fffdf0]' }}"
               :class="sidebarOpen ? 'justify-start' : 'justify-center px-2'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="sidebarOpen" class="transition-all duration-300">Laporan Pendapatan</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="px-4 py-4 border-t-2 border-retro-text flex-shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-3 font-bold border-2 border-[#202020] rounded bg-[#fffdf0] text-[#202020] hover:bg-retro-accent1 hover:text-white transition-all shadow-[2px_2px_0px_#202020] flex items-center gap-3 duration-300 cursor-pointer"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center px-2'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen" class="transition-all duration-300">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-retro-bg flex flex-col transition-all duration-300 ease-in-out">
        <!-- Top Bar -->
        <header class="bg-white border-b-4 border-retro-text px-8 py-4 flex items-center justify-between sticky top-0 z-10 text-retro-text flex-shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 border-2 border-retro-text bg-white hover:bg-retro-accent1 hover:text-white rounded-xl shadow-[2px_2px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-black text-retro-text">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-retro-text/60 font-semibold">@yield('page-subtitle', '')</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-retro-secondary/15 border-2 border-retro-text text-retro-text px-4 py-3 rounded-xl mb-4 shadow-[2px_2px_0px_#202020]" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                    <div class="w-6 h-6 rounded-full bg-retro-accent2 border border-retro-text flex items-center justify-center text-white text-xs font-bold">✓</div>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-retro-primary/15 border-2 border-retro-text text-retro-text px-4 py-3 rounded-xl mb-4 shadow-[2px_2px_0px_#202020]" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                    <div class="w-6 h-6 rounded-full bg-retro-primary border border-retro-text flex items-center justify-center text-white text-xs font-bold">✕</div>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <div class="px-8 py-6 flex-1">
            @yield('content')
        </div>
    </main>
</div>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
