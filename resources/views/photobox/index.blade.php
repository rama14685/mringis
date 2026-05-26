@extends('layouts.app')

@section('title', 'Mringis Photobox — Masukkan Token')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-retro-bg text-retro-text relative overflow-hidden p-4"
     x-data="tokenPage()">

    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-retro-accent1/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-retro-accent2/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-retro-accent1/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
        <!-- Grid pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23202020\" fill-opacity=\"0.02\"%3E%3Cpath d=\"M0 40L40 0H20L0 20M40 40V20L20 40\"%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')]"></div>
    </div>

    <!-- Camera decoration elements -->
    <div class="fixed top-8 left-8 opacity-15">
        <svg class="w-24 h-24 text-retro-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>
    <div class="fixed bottom-8 right-8 opacity-15 rotate-12">
        <svg class="w-32 h-32 text-retro-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>

    <!-- Main Card -->
    <div class="relative w-full max-w-lg z-10">
        <!-- Logo & Brand -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-64 h-64 mb-4 transform hover:scale-105 transition-transform duration-300">
                <img src="{{ asset('img/icon/icon.png') }}" class="w-full h-full object-contain" alt="Mringis Logo">
            </div>
            
            </h1>
            <p class="text-retro-text/70 text-lg font-bold">Abadikan momen terbaik Anda</p>
        </div>

        <!-- Token Card -->
        <div class="bg-white border-4 border-retro-text rounded-3xl p-8 shadow-[8px_8px_0px_0px_#202020] text-retro-text">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-black text-retro-text mb-1">Mulai Sesi Foto</h2>
                <p class="text-retro-text/70 text-sm font-semibold">Masukkan token 5 karakter dari staff</p>
            </div>

            <!-- Alert -->
            @if($errors->has('token'))
                <div class="flex items-center gap-3 bg-red-500/10 border-2 border-red-500/60 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm font-semibold">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $errors->first('token') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-500/10 border-2 border-red-500/60 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm font-semibold">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('photobox.validate') }}" method="POST" @submit="handleSubmit">
                @csrf
                <div class="mb-6">
                    <!-- Token Input Boxes -->
                    <div class="flex items-center justify-center gap-2 mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <input type="text" maxlength="1"
                               class="token-box w-14 h-16 bg-white border-4 border-retro-text rounded-xl text-center text-2xl font-black text-retro-text uppercase tracking-widest focus:outline-none focus:border-retro-accent1 focus:bg-retro-accent1/5 transition-all duration-200 caret-transparent shadow-[2px_2px_0px_0px_#202020]"
                               data-index="{{ $i }}"
                               autocomplete="off"
                               inputmode="text">
                        @endfor
                    </div>
                    <input type="hidden" name="token" x-model="token" id="token-hidden">
                    <p class="text-center text-xs text-retro-text/50 font-semibold">Tidak peka huruf besar/kecil</p>
                </div>

                <button type="submit"
                        id="submit-btn"
                        :disabled="token.length < 5"
                        class="w-full flex items-center justify-center gap-3 bg-retro-accent1 border-4 border-retro-text text-white font-black text-lg py-4 rounded-2xl transition-all duration-200 shadow-[4px_4px_0px_0px_#202020] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#202020] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Mulai Sesi Foto!
                </button>
            </form>
        </div>

        <!-- Admin link -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-retro-text/60 hover:text-retro-accent1 font-bold text-sm transition-colors">
                Admin Login →
            </a>
        </div>
    </div>
</div>

<style>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
</style>
@endsection

@push('scripts')
<script>
function tokenPage() {
    return {
        token: '',

        init() {
            this.setupTokenBoxes();
            // Pre-fill old value if validation failed
            const oldToken = '{{ old("token") }}';
            if (oldToken) {
                this.token = oldToken;
                const boxes = document.querySelectorAll('.token-box');
                oldToken.split('').forEach((char, i) => {
                    if (boxes[i]) boxes[i].value = char;
                });
            }
        },

        handleSubmit(e) {
            const boxes = document.querySelectorAll('.token-box');
            let combined = '';
            boxes.forEach(box => combined += box.value);
            this.token = combined.toLowerCase();
            document.getElementById('token-hidden').value = this.token;
        },

        setupTokenBoxes() {
            const boxes = document.querySelectorAll('.token-box');

            boxes.forEach((box, index) => {
                box.addEventListener('input', (e) => {
                    const val = e.target.value;
                    // Allow only alphanumeric
                    e.target.value = val.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();

                    if (e.target.value.length === 1 && index < boxes.length - 1) {
                        boxes[index + 1].focus();
                    }
                    this.updateToken();
                });

                box.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        boxes[index - 1].focus();
                        boxes[index - 1].value = '';
                        this.updateToken();
                    }
                    if (e.key === 'ArrowLeft' && index > 0) boxes[index - 1].focus();
                    if (e.key === 'ArrowRight' && index < boxes.length - 1) boxes[index + 1].focus();
                    if (e.key === 'Enter') {
                        this.updateToken();
                        if (this.token.length === 5) {
                            e.target.closest('form').submit();
                        }
                    }
                });

                box.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasted = e.clipboardData.getData('text').replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                    pasted.split('').forEach((char, i) => {
                        if (boxes[index + i]) boxes[index + i].value = char;
                    });
                    const nextIndex = Math.min(index + pasted.length, boxes.length - 1);
                    boxes[nextIndex].focus();
                    this.updateToken();
                });

                box.addEventListener('focus', () => box.select());
            });

            // Auto-focus first box
            if (boxes[0] && !'{{ old("token") }}') {
                boxes[0].focus();
            }
        },

        updateToken() {
            const boxes = document.querySelectorAll('.token-box');
            let combined = '';
            boxes.forEach(box => combined += box.value);
            this.token = combined.toLowerCase();
        }
    };
}
</script>
@endpush
