@extends('layouts.app')

@section('title', 'Foto Berhasil Dicetak! — Mringis')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-retro-bg text-retro-text relative overflow-hidden p-4">

    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-retro-accent1/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-retro-accent2/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    </div>

    <!-- Confetti Particles -->
    <div id="confetti-container" class="fixed inset-0 pointer-events-none z-0"></div>

    <div class="relative z-10 w-full max-w-lg text-center">
        <!-- Success Icon -->
        <div class="flex items-center justify-center mb-6">
            <div class="relative">
                <div class="w-28 h-28 bg-retro-accent2 border-4 border-retro-text rounded-full flex items-center justify-center shadow-[4px_4px_0px_0px_#202020] animate-bounce-slow">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <!-- Sparkles -->
                <div class="absolute -top-2 -right-2 text-retro-accent1 text-2xl animate-spin-slow">✨</div>
                <div class="absolute -bottom-1 -left-3 text-retro-accent1 text-xl animate-bounce">🎉</div>
            </div>
        </div>

        <h1 class="text-4xl font-black text-retro-text mb-2">Foto Berhasil!</h1>
        <p class="text-lg text-retro-text/70 font-semibold mb-6">Terima kasih telah menggunakan <span class="text-retro-accent1 font-black">Mringis Photobox</span></p>

        <!-- Result Card -->
        <div class="bg-white border-4 border-retro-text rounded-3xl p-8 mb-8 shadow-[8px_8px_0px_0px_#202020] text-retro-text">

            @if(session('result_image_url'))
            <div class="mb-6">
                <img src="{{ session('result_image_url') }}" alt="Hasil kolase foto"
                     class="max-w-full mx-auto rounded-2xl shadow-[4px_4px_0px_0px_#202020] border-4 border-retro-text">
            </div>
            @endif

            @if(session('success_token'))
            <div class="bg-retro-bg border-4 border-retro-text rounded-2xl p-4 mb-6 shadow-[3px_3px_0px_0px_#202020]">
                <p class="text-retro-text/50 text-xs font-bold mb-1">Token Sesi</p>
                <p class="font-mono font-black text-3xl text-retro-text tracking-[0.3em] uppercase">{{ session('success_token') }}</p>
                <p class="text-xs text-retro-text/50 font-bold mt-1">Token telah dinonaktifkan</p>
            </div>
            @endif

            <div class="space-y-3 text-sm text-left">
                <div class="flex items-center gap-3 text-retro-text font-bold">
                    <svg class="w-5 h-5 flex-shrink-0 text-retro-accent2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Foto kolase berhasil tersimpan
                </div>
                <div class="flex items-center gap-3 text-retro-text font-bold">
                    <svg class="w-5 h-5 flex-shrink-0 text-retro-accent2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Transaksi tercatat dalam sistem
                </div>
                <div class="flex items-center gap-3 text-retro-text font-bold">
                    <svg class="w-5 h-5 flex-shrink-0 text-retro-accent2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Foto siap dicetak!
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="space-y-3">
            <a href="{{ route('photobox.index') }}"
               class="flex items-center justify-center gap-3 bg-retro-accent1 border-4 border-retro-text text-white font-black text-lg py-4 px-8 rounded-2xl transition-all shadow-[4px_4px_0px_0px_#202020] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#202020] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none w-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Sesi Baru
            </a>
            <p class="text-retro-text/50 font-bold text-sm">Silakan ambil foto hasil cetak Anda dari mesin</p>
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
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animate-bounce-slow { animation: bounce-slow 2s ease-in-out infinite; }
.animate-spin-slow { animation: spin-slow 4s linear infinite; }

.confetti-piece {
    position: absolute;
    width: 10px;
    height: 10px;
    animation: confettiFall linear infinite;
    opacity: 0;
}
@keyframes confettiFall {
    0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}
</style>
@endsection

@push('scripts')
<script>
// Simple confetti effect
(function() {
    const container = document.getElementById('confetti-container');
    const colors = ['#a855f7', '#ec4899', '#f97316', '#22c55e', '#3b82f6', '#eab308'];

    for (let i = 0; i < 60; i++) {
        const piece = document.createElement('div');
        piece.className = 'confetti-piece';
        piece.style.left = Math.random() * 100 + 'vw';
        piece.style.top = '-20px';
        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        piece.style.animationDuration = (Math.random() * 3 + 2) + 's';
        piece.style.animationDelay = (Math.random() * 5) + 's';
        piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
        piece.style.width = (Math.random() * 10 + 5) + 'px';
        piece.style.height = (Math.random() * 10 + 5) + 'px';
        container.appendChild(piece);
    }
})();
</script>
@endpush
