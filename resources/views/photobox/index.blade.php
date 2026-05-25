@extends('layouts.app')

@section('title', 'Mringis Photobox — Masukkan Token')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-950 relative overflow-hidden p-4"
     x-data="tokenPage()">

    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-purple-600/15 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-600/15 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-indigo-600/10 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
        <!-- Grid pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%239C92AC\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M0 40L40 0H20L0 20M40 40V20L20 40\"%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')]"></div>
    </div>

    <!-- Camera decoration elements -->
    <div class="fixed top-8 left-8 opacity-10">
        <svg class="w-24 h-24 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>
    <div class="fixed bottom-8 right-8 opacity-10 rotate-12">
        <svg class="w-32 h-32 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>

    <!-- Main Card -->
    <div class="relative w-full max-w-lg z-10">
        <!-- Logo & Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 rounded-3xl shadow-2xl shadow-purple-500/40 mb-6 transform hover:scale-105 transition-transform">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h1 class="text-5xl font-black text-white mb-2 tracking-tight">
                <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-orange-400 bg-clip-text text-transparent">
                    Mringis
                </span>
            </h1>
            <p class="text-gray-400 text-lg">Abadikan momen terbaik Anda 📸</p>
        </div>

        <!-- Token Card -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl shadow-purple-900/30 p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">Mulai Sesi Foto</h2>
                <p class="text-gray-400 text-sm">Masukkan token 5 karakter yang diberikan oleh staff</p>
            </div>

            <!-- Alert -->
            @if($errors->has('token'))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl mb-6 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $errors->first('token') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl mb-6 text-sm">
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
                    <div class="flex items-center justify-center gap-3 mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <input type="text" maxlength="1"
                               class="token-box w-14 h-16 bg-white/10 border-2 border-white/20 rounded-xl text-center text-2xl font-black text-white uppercase tracking-widest focus:outline-none focus:border-purple-500 focus:bg-purple-500/10 focus:ring-2 focus:ring-purple-500/30 transition-all duration-200 caret-transparent"
                               data-index="{{ $i }}"
                               autocomplete="off"
                               inputmode="text">
                        @endfor
                    </div>
                    <input type="hidden" name="token" x-model="token" id="token-hidden">
                    <p class="text-center text-xs text-gray-500">Tidak peka huruf besar/kecil</p>
                </div>

                <button type="submit"
                        id="submit-btn"
                        :disabled="token.length < 5"
                        class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-purple-600 via-pink-600 to-orange-500 hover:from-purple-500 hover:via-pink-500 hover:to-orange-400 disabled:opacity-40 disabled:cursor-not-allowed text-white font-black text-lg py-4 rounded-2xl transition-all duration-200 shadow-xl shadow-purple-500/30 hover:shadow-purple-500/50 hover:-translate-y-1 active:translate-y-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Mulai Sesi Foto!
                </button>
            </form>
        </div>

        <!-- Admin link -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-400 text-xs transition-colors">
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
