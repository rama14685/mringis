@extends('layouts.app')

@section('title', 'Sesi Foto — Mringis Photobox')

@section('content')
@php
    /**
     * Resolved slots dalam persen (0-100) siap pakai di CSS.
     * Accessor Frame::getResolvedSlotsAttribute() menangani kedua skema:
     *   1. layout_coordinates (baru) — koordinat pixel absolut → dikonversi ke %
     *   2. slot_layout (lama) — sudah dalam %
     */
    $resolvedSlots = $frame->resolved_slots;
    $slotCount     = count($resolvedSlots);

    // Aspek rasio container kolase
    $aspectRatio   = $frame->aspect_ratio;          // float, e.g. 0.75 = portrait 3:4
    // CSS padding-bottom trick: padding-bottom = (h/w)*100% menjaga aspek rasio
    $paddingBottom = round((1 / max(0.1, $aspectRatio)) * 100, 2);
@endphp

<div class="min-h-screen bg-gray-950 flex flex-col"
     x-data="photoSession()"
     x-init="init()">

    {{-- ─── Header ─────────────────────────────────────────── --}}
    <header class="bg-gray-900/90 backdrop-blur-sm border-b border-gray-800 px-4 py-3 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-3">
            {{-- Phase label --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-pink-500 to-orange-500 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Fase 2 — Sesi Foto</p>
                    <h2 class="text-white font-bold text-sm truncate">{{ $frame->name }}</h2>
                </div>
            </div>

            {{-- Main Timer --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Sisa Waktu</p>
                    <p class="font-mono font-black text-2xl"
                       :class="mainTimeLeft <= 60 ? 'text-red-400 animate-pulse' : (mainTimeLeft <= 120 ? 'text-yellow-400' : 'text-white')"
                       x-text="formatTime(mainTimeLeft)">07:00</p>
                </div>
                <div class="w-12 h-12 relative flex-shrink-0">
                    <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#374151" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none"
                                :stroke="mainTimeLeft <= 60 ? '#f87171' : '#a855f7'"
                                stroke-width="3"
                                stroke-dasharray="100"
                                :stroke-dashoffset="100 - (mainTimeLeft / 420 * 100)"
                                stroke-linecap="round"
                                style="transition: stroke-dashoffset 1s linear"/>
                    </svg>
                </div>
            </div>

            {{-- Slot status dots --}}
            <div class="hidden sm:flex items-center gap-1.5 flex-shrink-0">
                @for($i = 0; $i < $slotCount; $i++)
                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-xs font-bold transition-all"
                     :class="slotImages[{{ $i }}] ? 'border-green-500 bg-green-500 text-white' : (activeSlot === {{ $i }} ? 'border-purple-500 bg-purple-500/20 text-purple-400' : 'border-gray-600 bg-gray-800 text-gray-500')">
                    <span x-show="slotImages[{{ $i }}]">✓</span>
                    <span x-show="!slotImages[{{ $i }}]">{{ $i + 1 }}</span>
                </div>
                @endfor
            </div>
        </div>
    </header>

    {{-- ─── Main Layout ─────────────────────────────────────── --}}
    <main class="flex-1 max-w-6xl mx-auto w-full px-4 py-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── Camera Feed Column ──────────────────────────────── --}}
        <div class="space-y-4">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                Kamera Live
            </h3>

            {{-- Camera Container --}}
            <div class="relative bg-black rounded-2xl overflow-hidden aspect-video shadow-2xl shadow-black/50"
                 id="camera-container">

                {{-- Live video --}}
                <video id="camera-video" autoplay playsinline muted
                       class="w-full h-full object-cover scale-x-[-1]"
                       style="display:block;"></video>

                {{-- Camera error state --}}
                <div id="camera-error"
                     class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-900 text-center p-6">
                    <svg class="w-12 h-12 text-red-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-red-400 font-bold mb-1">Kamera Tidak Tersedia</p>
                    <p class="text-gray-400 text-sm" id="camera-error-msg">Izinkan akses kamera untuk melanjutkan</p>
                    <button onclick="window.location.reload()"
                            class="mt-3 text-xs bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-xl transition-colors">
                        Coba Lagi
                    </button>
                </div>

                {{-- 5-second countdown overlay --}}
                <div id="countdown-overlay"
                     class="hidden absolute inset-0 flex items-center justify-center bg-black/75 z-10">
                    <div class="text-center">
                        <p id="countdown-num"
                           class="text-9xl font-black text-white drop-shadow-2xl animate-ping-slow leading-none">5</p>
                        <p class="text-white/70 text-xl mt-4 font-medium">Bersiap… Senyum! 😄</p>
                    </div>
                </div>

                {{-- Flash effect on capture --}}
                <div id="flash-overlay"
                     class="hidden absolute inset-0 bg-white z-20 pointer-events-none"></div>

                {{-- Active slot indicator --}}
                <div class="absolute bottom-3 left-3 bg-black/70 backdrop-blur-sm text-white text-sm px-3 py-1.5 rounded-xl">
                    Slot aktif: <span class="font-bold text-purple-300" x-text="'#' + (activeSlot + 1)"></span>
                </div>
            </div>

            {{-- Capture Controls --}}
            <div class="flex items-center gap-3">
                {{-- Jepret Button --}}
                <button @click="startCountdown()"
                        :disabled="!cameraReady || isCountingDown || isAutoRedirecting"
                        id="jepret-btn"
                        class="flex-1 flex items-center justify-center gap-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-400 hover:to-pink-400 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black text-xl py-5 rounded-2xl transition-all shadow-xl shadow-red-500/30 hover:shadow-red-500/50 hover:-translate-y-0.5 active:translate-y-0">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span x-text="isCountingDown ? 'Bersiap...' : '📸 Jepret!'"></span>
                </button>

                {{-- Flip Camera --}}
                <button @click="flipCamera()"
                        :disabled="!cameraReady"
                        class="w-14 h-14 bg-gray-800 hover:bg-gray-700 border border-gray-700 disabled:opacity-50 text-gray-300 rounded-2xl flex items-center justify-center transition-colors"
                        title="Ganti kamera (depan/belakang)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>

            {{-- Hidden capture canvas --}}
            <canvas id="capture-canvas" class="hidden"></canvas>

            {{-- Info hint --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-3.5 text-sm text-gray-400 flex items-start gap-2">
                <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Klik slot foto di kolase sebelah kanan untuk memilih slot mana yang ingin difoto ulang (retake).
            </div>
        </div>

        {{-- ── Collage Preview Column ──────────────────────────── --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-white font-bold text-lg">Kolase Foto</h3>
                <span class="text-xs text-gray-500 bg-gray-800 px-2.5 py-1 rounded-lg">
                    {{ $slotCount }} slot · {{ $frame->name }}
                </span>
            </div>

            {{-- Collage container with dynamic aspect ratio --}}
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4">
                {{--
                    Arsitektur z-index:
                    - Slot foto (z-index: 2)  → di BELAKANG frame
                    - Frame overlay PNG (z-index: 10) → di DEPAN, transparan
                    Teknik padding-bottom untuk aspek rasio dinamis
                --}}
                <div class="relative w-full mx-auto max-w-md" id="collage-container"
                     style="padding-bottom: {{ $paddingBottom }}%;">

                    <div class="absolute inset-0 bg-gray-800 rounded-xl overflow-hidden">

                        {{-- ── Slot Photos (behind frame) ─────────────── --}}
                        @foreach($resolvedSlots as $index => $slot)
                        <div class="absolute overflow-hidden cursor-pointer group transition-all duration-200"
                             style="left: {{ $slot['x'] }}%; top: {{ $slot['y'] }}%; width: {{ $slot['w'] }}%; height: {{ $slot['h'] }}%; z-index: 2;"
                             id="slot-{{ $index }}"
                             @click="setActiveSlot({{ $index }})"
                             :class="activeSlot === {{ $index }}
                                 ? 'ring-2 ring-purple-500 ring-offset-2 ring-offset-gray-800'
                                 : 'hover:ring-2 hover:ring-white/30 hover:ring-offset-1 hover:ring-offset-gray-800'">

                            {{-- Empty placeholder --}}
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gray-700/60 border-2 border-dashed border-gray-600"
                                 x-show="!slotImages[{{ $index }}]">
                                <span class="text-gray-500 text-xl font-bold mb-0.5">{{ $index + 1 }}</span>
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                            </div>

                            {{-- Captured photo --}}
                            <img id="slot-img-{{ $index }}"
                                 x-show="slotImages[{{ $index }}]"
                                 class="w-full h-full object-cover scale-x-[-1]"
                                 style="display:none;"
                                 alt="Foto slot {{ $index + 1 }}">

                            {{-- Retake hover overlay --}}
                            <div class="absolute inset-0 bg-purple-950/85 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 x-show="slotImages[{{ $index }}]" style="display:none;">
                                <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <p class="text-white text-xs font-bold">Foto Ulang</p>
                            </div>

                            {{-- Active slot indicator --}}
                            <div x-show="activeSlot === {{ $index }}"
                                 class="absolute top-1 right-1 w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center shadow-lg z-10">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        @endforeach

                        {{-- ── Frame PNG Overlay (on top, z-index: 10) ──── --}}
                        @if($frame->overlay_image)
                        <img src="{{ Storage::url($frame->overlay_image) }}"
                             class="absolute inset-0 w-full h-full object-fill pointer-events-none select-none"
                             style="z-index: 10;"
                             alt="Frame {{ $frame->name }}">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Proceed button (shown when all slots filled) --}}
            <div x-show="allSlotsFilled()" x-transition class="space-y-3">
                <div class="flex items-center gap-2 bg-green-500/10 border border-green-500/30 text-green-300 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Semua slot terisi! Klik untuk lanjut atau foto ulang mana saja.
                </div>

                <button @click="proceedToEdit()"
                        :disabled="isSaving"
                        class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 text-white font-black text-lg py-4 rounded-2xl transition-all shadow-xl shadow-purple-500/30 hover:-translate-y-0.5">
                    <template x-if="isSaving">
                        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    </template>
                    <span x-text="isSaving ? 'Menyimpan...' : '✨ Lanjut ke Edit'"></span>
                    <svg x-show="!isSaving" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
        </div>
    </main>

    {{-- ─── Auto-redirect overlay ──────────────────────────── --}}
    <div x-show="isAutoRedirecting" x-transition
         class="fixed inset-0 bg-black/92 flex flex-col items-center justify-center z-50">
        <div class="text-center">
            <div class="w-20 h-20 border-4 border-purple-500 border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
            <p class="text-2xl font-bold text-white mb-2">Waktu Habis!</p>
            <p class="text-gray-400">Menyimpan foto dan melanjutkan ke edit...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function photoSession() {
    return {
        // ── Timer ──────────────────────────────────────────────
        mainTimeLeft:    420,   // 7 menit
        mainTimer:       null,

        // ── Camera ─────────────────────────────────────────────
        cameraReady:     false,
        videoStream:     null,
        facingMode:      'user',

        // ── UI State ───────────────────────────────────────────
        isCountingDown:  false,
        isAutoRedirecting: false,
        isSaving:        false,

        // ── Slot Data ──────────────────────────────────────────
        slotCount:       {{ $slotCount }},
        slotImages:      Array({{ $slotCount }}).fill(null),
        activeSlot:      0,

        // ────────────────────────────────────────────────────────
        async init() {
            // Cleanup kamera saat meninggalkan halaman
            window.addEventListener('beforeunload', () => this.stopCamera());
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) this.stopCamera();
                else if (!this.cameraReady) this.startCamera();
            });

            await this.startCamera();
            this.startMainTimer();
        },

        // ── Camera ─────────────────────────────────────────────
        async startCamera() {
            try {
                // Hentikan stream lama jika ada sebelum meminta baru
                if (this.videoStream) {
                    this.videoStream.getTracks().forEach(t => t.stop());
                    this.videoStream = null;
                }
                this.cameraReady = false;

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: this.facingMode,
                        width:  { ideal: 1280 },
                        height: { ideal: 720  },
                    },
                    audio: false,
                });

                this.videoStream = stream;

                const video = document.getElementById('camera-video');
                video.srcObject = stream;

                // Gunakan event 'canplay' daripada await play() untuk mencegah:
                // "The play() request was interrupted by a new load request."
                await new Promise((resolve, reject) => {
                    video.oncanplay = resolve;
                    video.onerror   = reject;
                    // Timeout fallback: jika canplay tidak terpicu dalam 5 detik
                    setTimeout(resolve, 5000);
                });

                // play() dikembalikan sebagai promise — tangani silently
                video.play().catch(err => {
                    // Abaikan AbortError (interupsi saat stream diganti)
                    if (err.name !== 'AbortError') {
                        console.warn('[Camera] play() error:', err.name, err.message);
                    }
                });

                this.cameraReady = true;

            } catch (err) {
                console.error('[Camera] getUserMedia error:', err);
                document.getElementById('camera-error').classList.remove('hidden');
                document.getElementById('camera-video').style.display = 'none';

                const msgEl = document.getElementById('camera-error-msg');
                msgEl.textContent = ({
                    NotAllowedError:  'Akses kamera ditolak. Buka Pengaturan Browser → Izin Kamera.',
                    NotFoundError:    'Tidak ada kamera ditemukan pada perangkat ini.',
                    NotReadableError: 'Kamera sedang digunakan aplikasi lain.',
                    OverconstrainedError: 'Kamera tidak mendukung resolusi yang diminta.',
                })[err.name] ?? `Gagal mengakses kamera: ${err.message}`;
            }
        },

        stopCamera() {
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(t => t.stop());
                this.videoStream = null;
                this.cameraReady = false;
            }
        },

        async flipCamera() {
            this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
            await this.startCamera();
        },

        // ── Timer ─────────────────────────────────────────────
        startMainTimer() {
            this.mainTimer = setInterval(() => {
                this.mainTimeLeft--;
                if (this.mainTimeLeft <= 0) {
                    clearInterval(this.mainTimer);
                    this.handleTimerEnd();
                }
            }, 1000);
        },

        formatTime(seconds) {
            const s = Math.max(0, seconds);
            return `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
        },

        handleTimerEnd() {
            if (this.allSlotsFilled()) {
                this.proceedToEdit();
            } else {
                this.isAutoRedirecting = true;
                setTimeout(() => this.proceedToEdit(), 2000);
            }
        },

        // ── Slot Management ────────────────────────────────────
        setActiveSlot(index) {
            this.activeSlot = index;
        },

        allSlotsFilled() {
            return this.slotImages.every(img => img !== null);
        },

        // ── Capture ────────────────────────────────────────────
        startCountdown() {
            if (this.isCountingDown || !this.cameraReady) return;
            this.isCountingDown = true;

            let count      = 5;
            const overlay  = document.getElementById('countdown-overlay');
            const numEl    = document.getElementById('countdown-num');
            overlay.classList.remove('hidden');
            numEl.textContent = count;

            const tick = setInterval(() => {
                count--;
                numEl.textContent = count;
                if (count <= 0) {
                    clearInterval(tick);
                    overlay.classList.add('hidden');
                    this.capturePhoto();
                    this.isCountingDown = false;
                }
            }, 1000);
        },

        capturePhoto() {
            const video  = document.getElementById('camera-video');
            const canvas = document.getElementById('capture-canvas');
            const ctx    = canvas.getContext('2d');

            // Flash
            const flash = document.getElementById('flash-overlay');
            flash.classList.remove('hidden');
            setTimeout(() => flash.classList.add('hidden'), 150);

            // Set canvas dimensions dari video actual
            canvas.width  = video.videoWidth  || 1280;
            canvas.height = video.videoHeight || 720;

            // Render mirrored (cocok dengan preview video yang di-mirror CSS)
            ctx.save();
            ctx.scale(-1, 1);
            ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
            ctx.restore();

            const imageData = canvas.toDataURL('image/jpeg', 0.92);

            // Update slot
            this.slotImages[this.activeSlot] = imageData;

            // Update DOM langsung (tanpa reactive karena ini performa kritis)
            const imgEl = document.getElementById('slot-img-' + this.activeSlot);
            if (imgEl) {
                imgEl.src          = imageData;
                imgEl.style.display = 'block';
                // Sembunyikan placeholder
                const placeholder = document.querySelector(`#slot-${this.activeSlot} > div:first-child`);
                if (placeholder) placeholder.style.display = 'none';
                // Tampilkan hover overlay
                const hoverOverlay = document.querySelectorAll(`#slot-${this.activeSlot} > div`);
                hoverOverlay.forEach(el => { if (el.textContent.trim().includes('Foto Ulang')) el.style.display = ''; });
            }

            // Trigger Alpine reactivity
            this.slotImages = [...this.slotImages];

            // Auto-pindah ke slot kosong berikutnya
            const nextEmpty = this.slotImages.findIndex(img => img === null);
            if (nextEmpty !== -1) this.activeSlot = nextEmpty;
        },

        // ── Proceed to Edit ────────────────────────────────────
        async proceedToEdit() {
            if (this.isSaving) return;
            this.isSaving = true;

            clearInterval(this.mainTimer);
            this.stopCamera();

            // Auto-fill empty/null slots with placeholder (e.g. on timeout)
            for (let i = 0; i < this.slotImages.length; i++) {
                if (!this.slotImages[i]) {
                    try {
                        const canvas = document.getElementById('capture-canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = 640;
                        canvas.height = 480;
                        ctx.fillStyle = '#1f2937'; // gray-800
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        
                        ctx.fillStyle = '#9ca3af'; // gray-400
                        ctx.font = 'bold 24px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(`No Image (Slot ${i + 1})`, canvas.width / 2, canvas.height / 2);
                        
                        this.slotImages[i] = canvas.toDataURL('image/jpeg', 0.85);
                    } catch (canvasErr) {
                        console.error('Failed to create placeholder image:', canvasErr);
                    }
                }
            }

            try {
                const response = await fetch('{{ route('photobox.save-collage') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ slot_images: this.slotImages }),
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = '{{ route('photobox.edit') }}';
                } else {
                    alert('Gagal menyimpan foto. Silakan coba lagi.');
                    this.isSaving          = false;
                    this.isAutoRedirecting = false;
                    // Re-start camera
                    await this.startCamera();
                }
            } catch (err) {
                console.error('[Save Collage]', err);
                alert('Terjadi kesalahan koneksi.');
                this.isSaving          = false;
                this.isAutoRedirecting = false;
                await this.startCamera();
            }
        },
    };
}
</script>
@endpush
