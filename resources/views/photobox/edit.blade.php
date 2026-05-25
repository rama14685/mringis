@extends('layouts.app')

@section('title', 'Edit Kolase — Mringis Photobox')

@section('content')
@php
    $resolvedSlots = $frame->resolved_slots;
    $slotCount     = count($resolvedSlots);

    // Aspek rasio untuk canvas rendering
    $lcData        = $frame->layout_coordinates;
    $canvasRefW    = $lcData ? (int)($lcData['ref_w'] ?? 600) : 600;
    $canvasRefH    = $lcData ? (int)($lcData['ref_h'] ?? 600) : 600;
@endphp

<div class="min-h-screen bg-gray-950 flex flex-col"
     x-data="editCollage()"
     x-init="init()">

    {{-- ─── Header ─────────────────────────────────────────── --}}
    <header class="bg-gray-900/90 backdrop-blur-sm border-b border-gray-800 px-4 py-3 sticky top-0 z-20">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Fase 3</p>
                    <h2 class="text-white font-bold text-sm">Edit & Filter Kolase</h2>
                </div>
            </div>

            <div class="flex items-center gap-2 bg-gray-800 px-3 py-1.5 rounded-xl">
                <span class="text-xs text-gray-500">Token:</span>
                <span class="font-mono font-bold text-purple-400 uppercase text-sm">{{ $photoSession->token }}</span>
            </div>
        </div>
    </header>

    {{-- ─── Main Content ────────────────────────────────────── --}}
    <main class="flex-1 max-w-5xl mx-auto w-full px-4 py-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Canvas Preview ───────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white font-bold text-lg">Preview Kolase</h3>
                    <p class="text-gray-400 text-sm">Hasil akhir yang akan diunduh & dicetak</p>
                </div>
                <span class="text-xs text-gray-600">{{ $slotCount }} slot · {{ $frame->name }}</span>
            </div>

            <div class="relative bg-gray-900 border border-gray-800 rounded-2xl p-4 flex items-center justify-center">
                <canvas id="collage-canvas"
                        class="max-w-full rounded-xl shadow-2xl"
                        style="max-height: 65vh;"></canvas>

                {{-- Loading overlay --}}
                <div id="canvas-loading"
                     class="absolute inset-4 flex items-center justify-center bg-gray-900/90 rounded-xl">
                    <div class="text-center">
                        <div class="w-10 h-10 border-2 border-purple-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-gray-400 text-sm">Merender kolase...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Controls Column ──────────────────────────────── --}}
        <div class="space-y-5">
            <h3 class="text-white font-bold text-lg">Filter & Efek</h3>

            {{-- Filter Grid --}}
            <div class="grid grid-cols-2 gap-2">
                @php
                $filters = [
                    ['id' => 'normal',    'name' => 'Normal',   'icon' => '🎯', 'css' => 'none'],
                    ['id' => 'grayscale', 'name' => 'B&W',      'icon' => '⬛', 'css' => 'grayscale(100%)'],
                    ['id' => 'sepia',     'name' => 'Sepia',    'icon' => '🟤', 'css' => 'sepia(80%)'],
                    ['id' => 'vintage',   'name' => 'Vintage',  'icon' => '📷', 'css' => 'sepia(50%) contrast(1.1) brightness(0.9)'],
                    ['id' => 'vivid',     'name' => 'Vivid',    'icon' => '🌈', 'css' => 'saturate(1.8) contrast(1.1)'],
                    ['id' => 'cool',      'name' => 'Cool',     'icon' => '❄️', 'css' => 'hue-rotate(180deg) saturate(1.2)'],
                    ['id' => 'warm',      'name' => 'Warm',     'icon' => '🔥', 'css' => 'hue-rotate(-20deg) saturate(1.3) brightness(1.05)'],
                    ['id' => 'dark',      'name' => 'Dark',     'icon' => '🌑', 'css' => 'brightness(0.7) contrast(1.3)'],
                ];
                @endphp

                @foreach($filters as $filter)
                <button @click="applyFilter('{{ $filter['id'] }}', '{{ addslashes($filter['css']) }}')"
                        :class="activeFilter === '{{ $filter['id'] }}'
                            ? 'ring-2 ring-purple-500 bg-purple-500/10 border-purple-500/60 text-purple-300'
                            : 'border-gray-700 hover:border-gray-600 text-gray-300'"
                        class="flex items-center gap-2 p-3 bg-gray-900 border rounded-xl text-sm font-medium transition-all cursor-pointer">
                    <span class="text-xl leading-none">{{ $filter['icon'] }}</span>
                    <span>{{ $filter['name'] }}</span>
                </button>
                @endforeach
            </div>

            {{-- Manual Adjustments --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 space-y-4">
                <h4 class="text-sm font-semibold text-gray-300">Penyesuaian Manual</h4>

                @foreach([
                    ['key' => 'brightness', 'label' => 'Kecerahan', 'min' => 50, 'max' => 150],
                    ['key' => 'contrast',   'label' => 'Kontras',   'min' => 50, 'max' => 200],
                    ['key' => 'saturation', 'label' => 'Saturasi',  'min' => 0,  'max' => 200],
                ] as $adj)
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-xs text-gray-400">{{ $adj['label'] }}</label>
                        <span class="text-xs text-purple-400 font-mono" x-text="{{ $adj['key'] }} + '%'">100%</span>
                    </div>
                    <input type="range" min="{{ $adj['min'] }}" max="{{ $adj['max'] }}" x-model="{{ $adj['key'] }}"
                           @input="debouncedRender()"
                           class="w-full h-2 rounded-lg appearance-none cursor-pointer accent-purple-500 bg-gray-700">
                </div>
                @endforeach

                <button @click="resetAdjustments()"
                        class="w-full text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 py-2 rounded-lg transition-colors">
                    ↺ Reset ke Normal
                </button>
            </div>

            {{-- Print / Download Button --}}
            <div class="space-y-2">
                <button type="button" @click="submitForPrint()"
                        :disabled="!canvasReady || isPrinting"
                        class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black text-lg py-4 rounded-2xl transition-all shadow-xl shadow-purple-500/30 hover:-translate-y-0.5">
                    <template x-if="isPrinting">
                        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    </template>
                    <template x-if="!isPrinting">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </template>
                    <span x-text="isPrinting ? 'Mengunduh & Memproses...' : '🖨️ Unduh & Cetak Foto!'"></span>
                </button>
                <p class="text-xs text-gray-600 text-center">
                    Foto otomatis terunduh ke perangkat, lalu sesi selesai.
                </p>
            </div>
        </div>
    </main>

    {{-- Hidden form (POST ke server untuk mencatat transaksi) --}}
    <form id="print-form" action="{{ route('photobox.print') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="result_image" id="result-image-input">
    </form>
</div>
@endsection

@push('scripts')
<script>
function editCollage() {
    return {
        // ── Filter & Adjustment State ──────────────────────────
        activeFilter: 'normal',
        filterCss:    'none',
        brightness:   100,
        contrast:     100,
        saturation:   100,

        // ── Canvas State ───────────────────────────────────────
        canvasReady:  false,
        isPrinting:   false,
        renderTimer:  null,

        // ── Data from PHP ──────────────────────────────────────
        slotImages:     @json($slotImages),
        resolvedSlots:  @json($resolvedSlots),
        overlayImageUrl: @json($frame->overlay_image_url ?? null),
        canvasRefW:     {{ $canvasRefW }},
        canvasRefH:     {{ $canvasRefH }},

        // ── Init ───────────────────────────────────────────────
        async init() {
            await this.renderCollage();
        },

        // ── Filter ─────────────────────────────────────────────
        applyFilter(filterId, filterCss) {
            this.activeFilter = filterId;
            this.filterCss    = filterCss;
            this.renderCollage();
        },

        debouncedRender() {
            clearTimeout(this.renderTimer);
            this.renderTimer = setTimeout(() => this.renderCollage(), 80);
        },

        applyAdjustments() { this.renderCollage(); },

        resetAdjustments() {
            this.brightness  = 100;
            this.contrast    = 100;
            this.saturation  = 100;
            this.activeFilter = 'normal';
            this.filterCss   = 'none';
            this.renderCollage();
        },

        buildFilterString() {
            const base = `brightness(${this.brightness}%) contrast(${this.contrast}%) saturate(${this.saturation}%)`;
            return (this.filterCss === 'none' || !this.filterCss)
                ? base
                : `${this.filterCss} ${base}`;
        },

        // ── Canvas Rendering ───────────────────────────────────
        async renderCollage() {
            const canvas  = document.getElementById('collage-canvas');
            const ctx     = canvas.getContext('2d');
            const loading = document.getElementById('canvas-loading');
            loading.style.display = '';

            // Canvas dimensions = frame referensi (preserves aspect ratio)
            canvas.width  = this.canvasRefW;
            canvas.height = this.canvasRefH;

            // White background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // ── 1. Draw each slot photo (dengan filter) ─────────
            ctx.filter = this.buildFilterString();

            const photoPromises = this.slotImages.map((imgSrc, i) => {
                return new Promise(resolve => {
                    const slot = this.resolvedSlots[i];
                    if (!imgSrc || !slot) { resolve(); return; }

                    // resolvedSlots adalah persentase; konversi ke pixel canvas
                    const x = (slot.x / 100) * canvas.width;
                    const y = (slot.y / 100) * canvas.height;
                    const w = (slot.w / 100) * canvas.width;
                    const h = (slot.h / 100) * canvas.height;

                    const img = new Image();
                    img.onload = () => {
                        ctx.save();
                        ctx.beginPath();
                        // Rounded clip (opsional, 4px radius)
                        if (ctx.roundRect) ctx.roundRect(x, y, w, h, 4);
                        else ctx.rect(x, y, w, h);
                        ctx.clip();

                        // Center-crop: isi slot tanpa distorsi
                        const scale = Math.max(w / img.width, h / img.height);
                        const sw    = w / scale;
                        const sh    = h / scale;
                        const sx    = (img.width  - sw) / 2;
                        const sy    = (img.height - sh) / 2;
                        ctx.drawImage(img, sx, sy, sw, sh, x, y, w, h);
                        ctx.restore();
                        resolve();
                    };
                    img.onerror = () => resolve();
                    img.src     = imgSrc;
                });
            });

            await Promise.all(photoPromises);

            // ── 2. Draw frame overlay PNG on top (no filter) ─────
            ctx.filter = 'none';
            if (this.overlayImageUrl) {
                await new Promise(resolve => {
                    const overlay = new Image();
                    overlay.crossOrigin = 'anonymous';
                    overlay.onload = () => {
                        ctx.drawImage(overlay, 0, 0, canvas.width, canvas.height);
                        resolve();
                    };
                    overlay.onerror = () => resolve();
                    // Cache-bust untuk CORS di localhost
                    overlay.src = this.overlayImageUrl;
                });
            }

            loading.style.display = 'none';
            this.canvasReady = true;
        },

        // ── Submit: Auto-Download + POST to server ─────────────
        async submitForPrint() {
            if (this.isPrinting || !this.canvasReady) return;
            this.isPrinting = true;

            // Re-render untuk memastikan hasil terbaru dengan filter aktif
            await this.renderCollage();

            const canvas  = document.getElementById('collage-canvas');
            const dataUrl = canvas.toDataURL('image/png', 1.0);

            // ── 1. Trigger browser download ──────────────────────
            const link      = document.createElement('a');
            link.download   = `mringis-${Date.now()}.png`;
            link.href       = dataUrl;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // ── 2. POST ke server untuk mencatat transaksi & redirect sukses ──
            // Beri jeda sebentar agar download sempat terpicu sebelum navigate
            await new Promise(r => setTimeout(r, 500));

            document.getElementById('result-image-input').value = dataUrl;
            document.getElementById('print-form').submit();
        },
    };
}
</script>
@endpush
