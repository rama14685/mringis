@extends('layouts.admin')

@section('title', 'Tambah Frame')
@section('page-title', 'Tambah Frame Baru')
@section('page-subtitle', 'Buat template frame dengan mapping koordinat foto variatif')

@section('content')
<div class="max-w-5xl" x-data="frameEditor()" x-init="init()">
    <form action="{{ route('admin.frames.store') }}" method="POST" enctype="multipart/form-data" id="frame-form">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- ─── Kolom Kiri: Informasi Frame ─────────────────── --}}
            <div class="space-y-5">
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-5">
                    <h3 class="text-base font-bold text-white border-b border-gray-800 pb-3">Informasi Frame</h3>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nama Frame <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full bg-gray-800 border {{ $errors->has('name') ? 'border-red-500/60' : 'border-gray-700' }} rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all"
                               placeholder="Contoh: Vintage Strip, Kolase Duo" required>
                        @error('name')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all resize-none"
                                  placeholder="Deskripsi singkat frame...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Harga (Rp) <span class="text-red-400">*</span></label>
                        <div class="flex items-center gap-2 bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-purple-500/50 transition-all">
                            <span class="text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="price" value="{{ old('price', 10000) }}" min="0" step="1000"
                                   class="flex-1 bg-transparent text-white font-medium focus:outline-none" required>
                        </div>
                    </div>

                    {{-- Slot Count (hidden, diisi otomatis dari editor) --}}
                    <input type="hidden" name="slot_count" :value="slotCount || 4" id="slot-count-input">

                    {{-- Is Active --}}
                    <div class="flex items-center gap-3 pt-1">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                        <label for="is_active" class="text-sm font-medium text-gray-300 cursor-pointer">Frame Aktif</label>
                    </div>
                </div>

                {{-- Upload Frame PNG --}}
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                    <h3 class="text-base font-bold text-white mb-4 border-b border-gray-800 pb-3">Upload Frame PNG</h3>
                    <p class="text-xs text-gray-500 mb-4">Upload PNG transparan dari Canva. Area foto (bolong) akan terlihat di canvas editor di bawah.</p>

                    <div class="border-2 border-dashed border-gray-700 rounded-xl p-5 text-center hover:border-purple-500/40 transition-colors cursor-pointer"
                         @click="$refs.fileInput.click()"
                         @dragover.prevent
                         @drop.prevent="handleFileDrop($event)">
                        <input type="file" name="overlay_image" accept=".png" class="hidden" x-ref="fileInput"
                               @change="onFileChange($event)">

                        <div x-show="!previewUrl">
                            <svg class="w-10 h-10 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-400 text-sm font-medium">Klik atau drag & drop file PNG</p>
                            <p class="text-gray-600 text-xs mt-1">PNG transparan (area foto = bolong), max 5MB</p>
                        </div>

                        <div x-show="previewUrl" class="relative">
                            <div class="checkerboard rounded-lg overflow-hidden inline-block">
                                <img :src="previewUrl" class="max-h-32 object-contain">
                            </div>
                            <p class="text-gray-400 text-xs mt-2">Klik untuk ganti file</p>
                        </div>
                    </div>

                    {{-- Ref canvas size --}}
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div>
                            <label class="text-xs text-gray-500 block mb-1.5">Lebar Referensi Canvas (px)</label>
                            <input type="number" x-model.number="refW" min="100" max="2000" step="10"
                                   @change="resizeCanvas()"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-purple-500/50">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1.5">Tinggi Referensi Canvas (px)</label>
                            <input type="number" x-model.number="refH" min="100" max="2000" step="10"
                                   @change="resizeCanvas()"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-purple-500/50">
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4">
                    <button type="submit"
                            class="flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-purple-500/20 hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Frame
                    </button>
                    <a href="{{ route('admin.frames.index') }}" class="text-gray-400 hover:text-white text-sm font-medium transition-colors">Batal</a>
                </div>
            </div>

            {{-- ─── Kolom Kanan: Coordinate Editor ─────────────── --}}
            <div class="space-y-4">
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                    <div class="flex items-start justify-between mb-4 border-b border-gray-800 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-white">Editor Koordinat Slot</h3>
                            <p class="text-xs text-gray-500 mt-1">Gambar area foto dengan klik & drag pada canvas</p>
                        </div>
                        <button type="button" @click="clearAll()"
                                class="text-xs text-red-400 hover:text-red-300 bg-red-400/10 hover:bg-red-400/20 px-3 py-1.5 rounded-lg transition-colors">
                            Hapus Semua
                        </button>
                    </div>

                    {{-- Canvas Editor --}}
                    <div class="relative rounded-xl overflow-hidden border border-gray-700 cursor-crosshair mb-4" style="touch-action: none;">
                        <canvas x-ref="editorCanvas"
                                class="block w-full"
                                style="max-height: 480px; object-fit: contain;">
                        </canvas>
                        <div x-show="slots.length === 0 && !frameImageLoaded"
                             class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none p-4">
                            <svg class="w-10 h-10 text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <p class="text-gray-600 text-sm">Upload frame PNG, lalu klik & drag untuk mendefinisikan area foto</p>
                        </div>
                    </div>

                    {{-- Slot List --}}
                    <div x-show="slots.length > 0" class="space-y-2 mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Slot Terdefinisi (<span x-text="slots.length"></span>)</p>
                        <template x-for="(slot, index) in slots" :key="index">
                            <div class="flex items-center justify-between bg-gray-800 rounded-xl px-3 py-2.5 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 bg-purple-600 rounded-lg flex items-center justify-center text-xs font-bold text-white" x-text="index + 1"></div>
                                    <div class="text-xs text-gray-300 font-mono">
                                        X:<span class="text-purple-400" x-text="Math.round(slot.x)"></span>
                                        Y:<span class="text-purple-400" x-text="Math.round(slot.y)"></span>
                                        W:<span class="text-blue-400" x-text="Math.round(slot.w)"></span>
                                        H:<span class="text-blue-400" x-text="Math.round(slot.h)"></span>
                                    </div>
                                </div>
                                <button type="button" @click="removeSlot(index)"
                                        class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-300 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Instructions --}}
                    <div class="bg-blue-500/5 border border-blue-500/20 rounded-xl p-3 text-xs text-blue-300 space-y-1">
                        <p class="font-semibold text-blue-400">Petunjuk:</p>
                        <p>① Upload frame PNG dari Canva</p>
                        <p>② Klik & drag pada canvas untuk menandai area foto (slot) yang bolong/transparan</p>
                        <p>③ Ulangi untuk setiap slot foto</p>
                        <p>④ Hover slot list → klik X untuk hapus slot tertentu</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden input untuk layout_coordinates JSON --}}
        <input type="hidden" name="layout_coordinates" id="layout-coordinates-input">
    </form>
</div>
@endsection

@push('scripts')
<script>
function frameEditor(existingCoords = null) {
    return {
        // ── State ─────────────────────────────────────────────
        slots: [],
        isDrawing: false,
        startX: 0, startY: 0,
        currentRect: null,
        refW: 600,
        refH: 800,
        previewUrl: null,
        frameImageLoaded: false,
        frameImage: null,
        slotCount: 0,

        // ── Init ──────────────────────────────────────────────
        init() {
            this.$nextTick(() => {
                this.initCanvas();

                // Load existing coords if editing
                if (existingCoords && existingCoords.slots) {
                    this.refW  = existingCoords.ref_w || 600;
                    this.refH  = existingCoords.ref_h || 600;
                    this.slots = existingCoords.slots.map(s => ({...s}));
                    this.slotCount = this.slots.length;
                    this.updateInput();
                }

                this.resizeCanvas();
                this.redraw();
            });
        },

        // ── Canvas Setup ──────────────────────────────────────
        initCanvas() {
            const canvas = this.$refs.editorCanvas;
            if (!canvas) return;

            canvas.addEventListener('mousedown',  (e) => this.onMouseDown(e));
            canvas.addEventListener('mousemove',  (e) => this.onMouseMove(e));
            canvas.addEventListener('mouseup',    (e) => this.onMouseUp(e));
            canvas.addEventListener('mouseleave', (e) => { if (this.isDrawing) this.onMouseUp(e); });

            // Touch support
            canvas.addEventListener('touchstart', (e) => { e.preventDefault(); this.onMouseDown(e.touches[0]); });
            canvas.addEventListener('touchmove',  (e) => { e.preventDefault(); this.onMouseMove(e.touches[0]); });
            canvas.addEventListener('touchend',   (e) => { e.preventDefault(); this.onMouseUp(e.changedTouches[0]); });
        },

        resizeCanvas() {
            const canvas = this.$refs.editorCanvas;
            if (!canvas) return;
            canvas.width  = this.refW;
            canvas.height = this.refH;
            this.redraw();
        },

        // ── Mouse / Touch Handlers ────────────────────────────
        getCanvasPos(e) {
            const canvas = this.$refs.editorCanvas;
            const rect   = canvas.getBoundingClientRect();
            const scaleX = canvas.width  / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top)  * scaleY,
            };
        },

        onMouseDown(e) {
            const pos = this.getCanvasPos(e);
            this.startX    = pos.x;
            this.startY    = pos.y;
            this.isDrawing = true;
        },

        onMouseMove(e) {
            if (!this.isDrawing) return;
            const pos = this.getCanvasPos(e);
            this.currentRect = {
                x: Math.min(this.startX, pos.x),
                y: Math.min(this.startY, pos.y),
                w: Math.abs(pos.x - this.startX),
                h: Math.abs(pos.y - this.startY),
            };
            this.redraw();
        },

        onMouseUp(e) {
            if (!this.isDrawing) return;
            this.isDrawing = false;

            if (this.currentRect && this.currentRect.w > 8 && this.currentRect.h > 8) {
                this.slots.push({ ...this.currentRect });
                this.slotCount = this.slots.length;
                this.updateInput();
            }
            this.currentRect = null;
            this.redraw();
        },

        // ── Drawing ───────────────────────────────────────────
        redraw() {
            const canvas = this.$refs.editorCanvas;
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Background: checkerboard (shows transparency)
            this.drawCheckerboard(ctx, canvas.width, canvas.height);

            // Frame image
            if (this.frameImage) {
                ctx.drawImage(this.frameImage, 0, 0, canvas.width, canvas.height);
            }

            // Defined slots
            this.slots.forEach((slot, i) => {
                // Highlight fill
                ctx.fillStyle = 'rgba(168, 85, 247, 0.25)';
                ctx.fillRect(slot.x, slot.y, slot.w, slot.h);

                // Border
                ctx.strokeStyle = '#a855f7';
                ctx.lineWidth   = 2;
                ctx.strokeRect(slot.x, slot.y, slot.w, slot.h);

                // Corner markers
                const markerSize = 6;
                ctx.fillStyle = '#a855f7';
                [[slot.x, slot.y], [slot.x+slot.w-markerSize, slot.y],
                 [slot.x, slot.y+slot.h-markerSize], [slot.x+slot.w-markerSize, slot.y+slot.h-markerSize]]
                    .forEach(([mx, my]) => ctx.fillRect(mx, my, markerSize, markerSize));

                // Number label background
                const label  = `#${i + 1}`;
                const fs     = Math.max(12, Math.min(20, slot.w / 6));
                ctx.font     = `bold ${fs}px Inter, sans-serif`;
                const tw     = ctx.measureText(label).width;
                const pad    = 4;
                ctx.fillStyle = 'rgba(88, 28, 135, 0.85)';
                ctx.beginPath();
                ctx.roundRect(slot.x + 4, slot.y + 4, tw + pad * 2, fs + pad * 2, 4);
                ctx.fill();
                ctx.fillStyle = '#ffffff';
                ctx.fillText(label, slot.x + 4 + pad, slot.y + 4 + pad + fs * 0.85);
            });

            // Currently drawing rect
            if (this.currentRect) {
                ctx.fillStyle = 'rgba(168, 85, 247, 0.15)';
                ctx.fillRect(this.currentRect.x, this.currentRect.y, this.currentRect.w, this.currentRect.h);
                ctx.setLineDash([6, 4]);
                ctx.strokeStyle = '#c084fc';
                ctx.lineWidth   = 2;
                ctx.strokeRect(this.currentRect.x, this.currentRect.y, this.currentRect.w, this.currentRect.h);
                ctx.setLineDash([]);
            }
        },

        drawCheckerboard(ctx, w, h) {
            const size = Math.max(8, Math.floor(Math.min(w, h) / 40));
            for (let x = 0; x < w; x += size) {
                for (let y = 0; y < h; y += size) {
                    ctx.fillStyle = ((Math.floor(x/size) + Math.floor(y/size)) % 2 === 0)
                        ? '#2d3748' : '#1a202c';
                    ctx.fillRect(x, y, size, size);
                }
            }
        },

        // ── File Handling ─────────────────────────────────────
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.loadImageFile(file);
        },

        handleFileDrop(e) {
            const file = e.dataTransfer.files[0];
            if (!file || !file.type.includes('png')) { alert('Hanya file PNG yang diterima.'); return; }
            // Manually set to file input
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.fileInput.files = dt.files;
            this.loadImageFile(file);
        },

        loadImageFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewUrl = e.target.result;
                const img = new Image();
                img.onload = () => {
                    this.frameImage = img;
                    this.frameImageLoaded = true;

                    // Auto-update refW & refH dari natural image size (capped)
                    const maxDim = 1000;
                    const scale  = Math.min(1, maxDim / Math.max(img.width, img.height));
                    this.refW = Math.round(img.width  * scale);
                    this.refH = Math.round(img.height * scale);
                    this.resizeCanvas();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        // ── Slot Management ───────────────────────────────────
        removeSlot(index) {
            this.slots.splice(index, 1);
            this.slotCount = this.slots.length;
            this.redraw();
            this.updateInput();
        },

        clearAll() {
            this.slots    = [];
            this.slotCount = 0;
            this.redraw();
            this.updateInput();
        },

        updateInput() {
            const payload = {
                ref_w: this.refW,
                ref_h: this.refH,
                slots: this.slots.map(s => ({
                    x: Math.round(s.x),
                    y: Math.round(s.y),
                    w: Math.round(s.w),
                    h: Math.round(s.h),
                })),
            };
            document.getElementById('layout-coordinates-input').value = JSON.stringify(payload);
            document.getElementById('slot-count-input').value = this.slots.length || 4;
        },
    };
}
</script>

<style>
.checkerboard {
    background-image:
        linear-gradient(45deg, #374151 25%, transparent 25%),
        linear-gradient(-45deg, #374151 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #374151 75%),
        linear-gradient(-45deg, transparent 75%, #374151 75%);
    background-size: 16px 16px;
    background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
    background-color: #1f2937;
}
</style>
@endpush
