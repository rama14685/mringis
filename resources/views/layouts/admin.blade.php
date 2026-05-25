<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — Mringis Photobox</title>
    <meta name="description" content="Panel Admin Mringis Photobox">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200; }
        .sidebar-link:hover { @apply bg-white/10 text-white; }
        .sidebar-link.active { @apply bg-white text-purple-700 shadow-lg shadow-purple-500/20; }
        .sidebar-link.active svg { @apply text-purple-600; }
    </style>
</head>
<body class="h-full bg-gray-950 text-white">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-72 flex-shrink-0 bg-gradient-to-b from-purple-900 via-purple-800 to-indigo-900 flex flex-col shadow-2xl">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-white font-bold text-lg leading-none">Mringis</h1>
                <p class="text-purple-200 text-xs">Photobox System</p>
            </div>
        </div>

        <!-- Admin Info -->
        <div class="px-6 py-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-purple-300">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-purple-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.frames.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.frames*') ? 'active' : 'text-purple-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Manajemen Frame
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : 'text-purple-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Laporan Pendapatan
            </a>
        </nav>

        <!-- Logout -->
        <div class="px-4 py-4 border-t border-white/10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="sidebar-link text-purple-100 w-full hover:bg-red-500/20 hover:text-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gray-950">
        <!-- Top Bar -->
        <header class="bg-gray-900/80 backdrop-blur-sm border-b border-gray-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-bold text-white">@yield('page-title', 'Dashboard')</h2>
                <p class="text-sm text-gray-400">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-300 px-4 py-3 rounded-xl mb-4" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl mb-4" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <div class="px-8 py-6">
            @yield('content')
        </div>
    </main>
</div>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
