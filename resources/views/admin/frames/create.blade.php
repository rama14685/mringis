@extends('layouts.admin')

@section('title', 'Tambah Frame')
@section('page-title', 'Tambah Frame Baru')
@section('page-subtitle', 'Buat template frame dengan mapping koordinat foto variatif')

@section('content')
<div class="max-w-5xl text-retro-text" x-data="frameEditor()" x-init="init()">
    <form action="{{ route('admin.frames.store') }}" method="POST" enctype="multipart/form-data" id="frame-form">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- ─── Kolom Kiri: Informasi Frame ─────────────────── --}}
            <div class="space-y-5">
                <div class="bg-white border-4 border-retro-text rounded-2xl p-6 space-y-5 shadow-[4px_4px_0px_#202020]">
                    <h3 class="text-base font-black border-b-4 border-retro-text pb-3 uppercase tracking-wider">Informasi Frame</h3>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Nama Frame <span class="text-retro-primary font-black">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full bg-white border-2 border-retro-text rounded-xl px-4 py-3 text-retro-text placeholder-retro-text/40 font-bold focus:outline-none focus:ring-2 focus:ring-retro-primary transition-all"
                               placeholder="Contoh: Vintage Strip, Kolase Duo" required>
                        @error('name')<p class="text-retro-primary text-xs font-bold mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  class="w-full bg-white border-2 border-retro-text rounded-xl px-4 py-3 text-retro-text placeholder-retro-text/40 font-bold focus:outline-none focus:ring-2 focus:ring-retro-primary transition-all resize-none"
                                  placeholder="Deskripsi singkat frame...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Harga (Rp) <span class="text-retro-primary font-black">*</span></label>
                        <div class="flex items-center gap-2 bg-white border-2 border-retro-text rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-retro-primary transition-all">
                            <span class="text-retro-text/60 text-sm font-black">Rp</span>
                            <input type="number" name="price" value="{{ old('price', 10000) }}" min="0" step="1000"
                                   class="flex-1 bg-transparent text-retro-text font-black focus:outline-none" required>
                        </div>
                    </div>

                    {{-- Slot Count (hidden, diisi otomatis dari editor) --}}
                    <input type="hidden" name="slot_count" :value="slotCount || 4" id="slot-count-input">

                    {{-- Is Active --}}
                    <div class="flex items-center gap-3 pt-1">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 border-2 border-retro-text rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-retro-text after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-retro-secondary"></div>
                        </label>
                        <label for="is_active" class="text-sm font-bold cursor-pointer select-none">Frame Aktif</label>
                    </div>
                </div>

                {{-- Upload Frame PNG --}}
                <div class="bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
                    <h3 class="text-base font-black mb-4 border-b-4 border-retro-text pb-3 uppercase tracking-wider">Upload Frame PNG</h3>
                    <p class="text-xs text-retro-text/60 font-semibold mb-4">Upload PNG transparan dari Canva. Area foto (bolong) akan terlihat di canvas editor di samping.</p>

                    <div class="border-4 border-dashed border-retro-text bg-retro-bg/40 rounded-xl p-5 text-center hover:bg-retro-primary/5 transition-colors cursor-pointer"
                         @click="$refs.fileInput.click()"
                         @dragover.prevent
                         @drop.prevent="handleFileDrop($event)">
                         <input type="file" name="overlay_image" accept=".png" class="hidden" x-ref="fileInput"
                                @change="onFileChange($event)">

                        <div x-show="!previewUrl">
                            <svg class="w-10 h-10 text-retro-text mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm font-black">Klik atau drag & drop file PNG</p>
                            <p class="text-retro-text/60 text-xs mt-1 font-semibold">PNG transparan (area foto = bolong), max 5MB</p>
                        </div>

                        <div x-show="previewUrl" class="relative">
                            <div class="checkerboard rounded-lg overflow-hidden inline-block p-1 border-2 border-retro-text">
                                <img :src="previewUrl" class="max-h-32 object-contain">
                            </div>
                            <p class="text-retro-text/60 text-xs mt-2 font-bold">Klik untuk ganti file</p>
                        </div>
                    </div>

                    {{-- Ref canvas size --}}
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div>
                            <label class="text-xs font-bold block mb-1.5">Lebar Referensi Canvas (px)</label>
                            <input type="number" x-model.number="refW" min="100" max="2000" step="10"
                                   @change="resizeCanvas()"
                                   class="w-full bg-white border-2 border-retro-text rounded-lg px-3 py-2 text-retro-text font-bold text-sm focus:outline-none focus:ring-1 focus:ring-retro-primary">
                        </div>
                        <div>
                            <label class="text-xs font-bold block mb-1.5">Tinggi Referensi Canvas (px)</label>
                            <input type="number" x-model.number="refH" min="100" max="2000" step="10"
                                   @change="resizeCanvas()"
                                   class="w-full bg-white border-2 border-retro-text rounded-lg px-3 py-2 text-retro-text font-bold text-sm focus:outline-none focus:ring-1 focus:ring-retro-primary">
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4">
                    <button type="submit"
                            class="flex items-center gap-2 bg-retro-primary border-4 border-retro-text text-white font-black px-6 py-3.5 rounded-xl transition-all shadow-[4px_4px_0px_#202020] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#202020] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none cursor-pointer">
                        <svg class="w-5 h-5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Frame
                    </button>
                    <a href="{{ route('admin.frames.index') }}" class="text-retro-text hover:text-retro-primary font-black text-sm transition-colors">Batal</a>
                </div>
            </div>

            {{-- ─── Kolom Kanan: Coordinate Editor ─────────────── --}}
            <div class="space-y-4">
                <div class="bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
                    <div class="flex items-center justify-between mb-4 border-b-4 border-retro-text pb-3">
                        <div>
                            <h3 class="text-base font-black">Editor Koordinat Slot</h3>
                            <p class="text-xs text-retro-text/60 font-semibold mt-1">Gambar area foto dengan klik & drag pada canvas</p>
                        </div>
                        <button type="button" @click="clearAll()"
                                class="text-xs font-bold text-white bg-retro-primary border-2 border-retro-text px-3 py-1.5 rounded-lg shadow-[2px_2px_0px_#202020] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none cursor-pointer">
                            Hapus Semua
                        </button>
                    </div>

                    {{-- Canvas Editor Container --}}
                    <div class="relative rounded-2xl overflow-hidden border-4 border-retro-text cursor-crosshair mb-4 bg-white shadow-[4px_4px_0px_#202020]" 
                         style="touch-action: none; max-width: 100%;">
                        <div class="relative w-full" :style="{ aspectRatio: refW + '/' + refH }">
                            <canvas x-ref="editorCanvas"
                                    class="absolute inset-0 w-full h-full block">
                            </canvas>
                            
                            {{-- Drag placeholder --}}
                            <div x-show="isDrawing && dragRect" 
                                 class="absolute border-2 border-dashed border-retro-primary bg-retro-primary/10 pointer-events-none"
                                 :style="{
                                     left: dragRect.left,
                                     top: dragRect.top,
                                     width: dragRect.width,
                                     height: dragRect.height
                                 }">
                            </div>

                            {{-- Rendered Slot DIVs --}}
                            <template x-for="(slot, index) in slots" :key="index">
                                <div class="absolute border-4 border-retro-text bg-retro-secondary/20 flex items-center justify-center group pointer-events-none"
                                     :style="{
                                         left: (slot.x / refW) * 100 + '%',
                                         top: (slot.y / refH) * 100 + '%',
                                         width: (slot.w / refW) * 100 + '%',
                                         height: (slot.h / refH) * 100 + '%',
                                     }">
                                    
                                    {{-- Slot Number Label --}}
                                    <div class="bg-retro-text text-white text-xs font-black px-2 py-1 border-2 border-retro-text rounded shadow-[2px_2px_0px_#202020] pointer-events-none"
                                         x-text="'#' + (index + 1)">
                                    </div>

                                    {{-- Delete Button 'x' --}}
                                    <button type="button"
                                            @click.stop="removeSlot(index)"
                                            class="absolute -top-3 -right-3 w-7 h-7 bg-retro-primary text-white border-2 border-retro-text rounded-full flex items-center justify-center font-black shadow-[2px_2px_0px_#202020] hover:bg-red-700 transition-all text-xs z-10 cursor-pointer pointer-events-auto">
                                        ✕
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="slots.length === 0 && !frameImageLoaded"
                         class="bg-retro-bg border-2 border-dashed border-retro-text rounded-xl p-4 text-center mb-4">
                        <p class="text-retro-text/60 text-xs font-bold">Upload frame PNG, lalu klik & drag untuk mendefinisikan area foto</p>
                    </div>

                    {{-- Slot List --}}
                    <div x-show="slots.length > 0" class="space-y-2 mb-4">
                        <p class="text-xs font-black text-retro-text/60 uppercase tracking-wider">Slot Terdefinisi (<span x-text="slots.length"></span>)</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="(slot, index) in slots" :key="index">
                                <div class="flex items-center justify-between bg-retro-bg border-2 border-retro-text rounded-xl px-3 py-2.5 group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 bg-retro-text border border-retro-text rounded-lg flex items-center justify-center text-xs font-black text-white" x-text="index + 1"></div>
                                        <div class="text-xs text-retro-text font-black font-mono">
                                            X:<span class="text-retro-primary" x-text="Math.round(slot.x)"></span>
                                            Y:<span class="text-retro-primary" x-text="Math.round(slot.y)"></span>
                                            W:<span class="text-retro-secondary" x-text="Math.round(slot.w)"></span>
                                            H:<span class="text-retro-secondary" x-text="Math.round(slot.h)"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeSlot(index)"
                                            class="text-retro-primary hover:text-red-700 font-black cursor-pointer transition-all">
                                        <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Instructions --}}
                    <div class="bg-retro-bg border-2 border-retro-text rounded-xl p-4 text-xs space-y-1 shadow-[2px_2px_0px_#202020]">
                        <p class="font-black text-retro-primary uppercase">Petunjuk:</p>
                        <p class="font-semibold">① Upload frame PNG dari Canva</p>
                        <p class="font-semibold">② Klik & drag pada canvas untuk menandai area foto (slot) yang bolong/transparan</p>
                        <p class="font-semibold">③ Ulangi untuk setiap slot foto</p>
                        <p class="font-semibold">④ Hover slot list → klik X untuk hapus slot tertentu, atau klik ✕ di atas canvas</p>
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
        dragRect: null,
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
                x: Math.max(0, Math.min(canvas.width, (e.clientX - rect.left) * scaleX)),
                y: Math.max(0, Math.min(canvas.height, (e.clientY - rect.top) * scaleY)),
            };
        },

        onMouseDown(e) {
            const pos = this.getCanvasPos(e);
            this.startX    = pos.x;
            this.startY    = pos.y;
            this.isDrawing = true;
            this.currentRect = { x: this.startX, y: this.startY, w: 0, h: 0 };
            this.dragRect = { left: '0%', top: '0%', width: '0%', height: '0%' };
        },

        onMouseMove(e) {
            if (!this.isDrawing) return;
            const pos = this.getCanvasPos(e);
            
            // Limit coordinates inside boundaries
            const currentX = Math.max(0, Math.min(this.refW, pos.x));
            const currentY = Math.max(0, Math.min(this.refH, pos.y));
            
            this.currentRect = {
                x: Math.min(this.startX, currentX),
                y: Math.min(this.startY, currentY),
                w: Math.abs(currentX - this.startX),
                h: Math.abs(currentY - this.startY),
            };
            
            // Update real-time drag placeholder in percentage
            this.dragRect = {
                left: (this.currentRect.x / this.refW) * 100 + '%',
                top: (this.currentRect.y / this.refH) * 100 + '%',
                width: (this.currentRect.w / this.refW) * 100 + '%',
                height: (this.currentRect.h / this.refH) * 100 + '%',
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
            this.dragRect = null;
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
        },

        drawCheckerboard(ctx, w, h) {
            const size = Math.max(8, Math.floor(Math.min(w, h) / 40));
            for (let x = 0; x < w; x += size) {
                for (let y = 0; y < h; y += size) {
                    ctx.fillStyle = ((Math.floor(x/size) + Math.floor(y/size)) % 2 === 0)
                        ? '#e5e7eb' : '#f9fafb';
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
        linear-gradient(45deg, #e5e7eb 25%, transparent 25%),
        linear-gradient(-45deg, #e5e7eb 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #e5e7eb 75%),
        linear-gradient(-45deg, transparent 75%, #e5e7eb 75%);
    background-size: 16px 16px;
    background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
    background-color: #f9fafb;
}
</style>
@endpush
