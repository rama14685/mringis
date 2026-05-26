@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-retro-bg text-retro-text p-4"
     x-data="{ showPass: false }">

    <!-- Animated background orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-retro-accent1/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-retro-accent2/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-retro-accent1/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Card -->
        <div class="bg-white border-4 border-retro-text rounded-3xl p-8 shadow-[8px_8px_0px_0px_#202020] text-retro-text">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-retro-bg border-4 border-retro-text rounded-2xl shadow-[4px_4px_0px_0px_#202020] mb-4 p-2">
                    <img src="{{ asset('img/icon/icon.png') }}" class="w-full h-full object-contain" alt="Mringis Logo">
                </div>
                <h1 class="text-3xl font-black text-retro-text mb-1 tracking-tight">Mringis</h1>
                <p class="text-retro-text/60 text-sm font-semibold">Masuk ke Panel Admin</p>
            </div>

            <!-- Alerts -->
            @if(session('error'))
                <div class="flex items-center gap-2 bg-red-500/10 border-2 border-red-500/60 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm font-semibold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-bold text-retro-text mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full bg-white border-4 border-retro-text rounded-xl px-4 py-3 text-retro-text placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-retro-accent1/30 transition-all shadow-[2px_2px_0px_0px_#202020]"
                           placeholder="admin@mringis.com" required autocomplete="email">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-retro-text mb-2">Password</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password"
                               class="w-full bg-white border-4 border-retro-text rounded-xl px-4 py-3 pr-12 text-retro-text placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-retro-accent1/30 transition-all shadow-[2px_2px_0px_0px_#202020]"
                               placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" @click="showPass = !showPass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-retro-text/60 hover:text-retro-text transition-colors">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember"
                               class="w-4 h-4 rounded border-2 border-retro-text bg-white text-retro-accent1 focus:ring-retro-accent1/30">
                        <span class="text-sm text-retro-text/70 font-semibold">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" id="login-btn"
                        class="w-full bg-retro-accent1 border-4 border-retro-text text-white font-black py-3.5 rounded-xl transition-all duration-200 shadow-[4px_4px_0px_0px_#202020] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#202020] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

        <p class="text-center text-retro-text/50 font-bold text-xs mt-6">© 2026 Mringis Photobox. All rights reserved.</p>
    </div>
</div>
</div>
@endsection
