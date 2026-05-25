@extends('layouts.admin')

@section('title', 'Edit Frame')
@section('page-title', 'Edit Frame')
@section('page-subtitle', 'Ubah konfigurasi: ' . $frame->name)

@section('content')
@php
    $existingCoords = $frame->layout_coordinates ? json_encode($frame->layout_coordinates) : 'null';
@endphp

<div class="max-w-5xl" x-data="frameEditor({{ $existingCoords }})" x-init="init()">
    <form action="{{ route('admin.frames.update', $frame) }}" method="POST" enctype="multipart/form-data" id="frame-form">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- ─── Kolom Kiri: Informasi Frame ─────────────────── --}}
            <div class="space-y-5">
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-5">
                    <h3 class="text-base font-bold text-white border-b border-gray-800 pb-3">Informasi Frame</h3>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nama Frame <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $frame->name) }}"
                               class="w-full bg-gray-800 border {{ $errors->has('name') ? 'border-red-500/60' : 'border-gray-700' }} rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all"
                               required>
                        @error('name')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all resize-none">{{ old('description', $frame->description) }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Harga (Rp) <span class="text-red-400">*</span></label>
                        <div class="flex items-center gap-2 bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-purple-500/50 transition-all">
                            <span class="text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="price" value="{{ old('price', $frame->price) }}" min="0" step="1000"
                                   class="flex-1 bg-transparent text-white font-medium focus:outline-none" required>
                        </div>
                    </div>

                    <input type="hidden" name="slot_count" :value="slotCount || {{ $frame->slot_count }}" id="slot-count-input">

                    {{-- Is Active --}}
                    <div class="flex items-center gap-3 pt-1">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ old('is_active', $frame->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                        <label for="is_active" class="text-sm font-medium text-gray-300 cursor-pointer">Frame Aktif</label>
                    </div>
                </div>

                {{-- Upload Frame PNG --}}
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                    <h3 class="text-base font-bold text-white mb-4 border-b border-gray-800 pb-3">Gambar Frame PNG</h3>

                    @if($frame->overlay_image)
                    <div class="flex items-center gap-3 bg-gray-800 rounded-xl p-3 mb-4">
                        <div class="checkerboard rounded-lg overflow-hidden w-16 h-16 flex-shrink-0">
                            <img src="{{ Storage::url($frame->overlay_image) }}" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <p class="text-sm text-gray-300 font-medium">Frame Saat Ini</p>
                            <p class="text-xs text-gray-500">Upload baru untuk mengganti</p>
                        </div>
                    </div>
                    @endif

                    <div class="border-2 border-dashed border-gray-700 rounded-xl p-5 text-center hover:border-purple-500/40 transition-colors cursor-pointer"
                         @click="$refs.fileInput.click()"
                         @dragover.prevent
                         @drop.prevent="handleFileDrop($event)">
                        <input type="file" name="overlay_image" accept=".png" class="hidden" x-ref="fileInput"
                               @change="onFileChange($event)">

                        <div x-show="!previewUrl">
                            <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-400 text-sm">Klik untuk upload PNG baru</p>
                        </div>
                        <div x-show="previewUrl">
                            <div class="checkerboard rounded-lg overflow-hidden inline-block">
                                <img :src="previewUrl" class="max-h-24 object-contain">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div>
                            <label class="text-xs text-gray-500 block mb-1.5">Lebar Ref. Canvas (px)</label>
                            <input type="number" x-model.number="refW" min="100" max="2000" step="10"
                                   @change="resizeCanvas()"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-purple-500/50">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 block mb-1.5">Tinggi Ref. Canvas (px)</label>
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
                        Update Frame
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
                            <p class="text-xs text-gray-500 mt-1">Gambar area foto dengan klik & drag</p>
                        </div>
                        <button type="button" @click="clearAll()"
                                class="text-xs text-red-400 hover:text-red-300 bg-red-400/10 hover:bg-red-400/20 px-3 py-1.5 rounded-lg transition-colors">
                            Hapus Semua
                        </button>
                    </div>

                    @if($frame->overlay_image)
                    <div class="flex items-center gap-2 mb-3">
                        <button type="button" @click="loadExistingFrameImage()"
                                class="flex items-center gap-2 text-xs bg-purple-600/20 hover:bg-purple-600/30 text-purple-400 border border-purple-500/30 px-3 py-1.5 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Tampilkan Frame di Canvas
                        </button>
                    </div>
                    @endif

                    <div class="relative rounded-xl overflow-hidden border border-gray-700 cursor-crosshair mb-4" style="touch-action: none;">
                        <canvas x-ref="editorCanvas" class="block w-full" style="max-height: 480px;"></canvas>
                        <div x-show="slots.length === 0 && !frameImageLoaded"
                             class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none p-4">
                            <svg class="w-10 h-10 text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <p class="text-gray-600 text-sm">Klik & drag untuk mendefinisikan slot foto</p>
                        </div>
                    </div>

                    {{-- Slot list --}}
                    <div x-show="slots.length > 0" class="space-y-2 mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Slot (<span x-text="slots.length"></span>)</p>
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

                    <div class="bg-blue-500/5 border border-blue-500/20 rounded-xl p-3 text-xs text-blue-300 space-y-1">
                        <p class="font-semibold text-blue-400">Petunjuk:</p>
                        <p>① Klik "Tampilkan Frame di Canvas" untuk memuat gambar frame</p>
                        <p>② Klik & drag pada area foto (transparan) untuk mendefinisikan slot</p>
                        <p>③ Hover slot → klik X untuk hapus</p>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="layout_coordinates" id="layout-coordinates-input">
    </form>
</div>
@endsection

@push('scripts')
<script>
function frameEditor(existingCoords) {
    return {
        slots: [],
        isDrawing: false,
        startX: 0, startY: 0,
        currentRect: null,
        refW: existingCoords ? (existingCoords.ref_w || 600) : 600,
        refH: existingCoords ? (existingCoords.ref_h || 600) : 600,
        previewUrl: null,
        frameImageLoaded: false,
        frameImage: null,
        slotCount: 0,
        existingFrameUrl: @json(isset($frame) && $frame->overlay_image ? Storage::url($frame->overlay_image) : null),

        init() {
            this.$nextTick(() => {
                this.initCanvas();
                if (existingCoords && existingCoords.slots) {
                    this.slots    = existingCoords.slots.map(s => ({...s}));
                    this.slotCount = this.slots.length;
                    this.updateInput();
                }
                this.resizeCanvas();
                this.redraw();
            });
        },

        initCanvas() {
            const canvas = this.$refs.editorCanvas;
            if (!canvas) return;
            canvas.addEventListener('mousedown',  (e) => this.onMouseDown(e));
            canvas.addEventListener('mousemove',  (e) => this.onMouseMove(e));
            canvas.addEventListener('mouseup',    (e) => this.onMouseUp(e));
            canvas.addEventListener('mouseleave', (e) => { if (this.isDrawing) this.onMouseUp(e); });
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

        getCanvasPos(e) {
            const canvas = this.$refs.editorCanvas;
            const rect   = canvas.getBoundingClientRect();
            return {
                x: (e.clientX - rect.left) * (canvas.width / rect.width),
                y: (e.clientY - rect.top)  * (canvas.height / rect.height),
            };
        },

        onMouseDown(e) {
            const pos = this.getCanvasPos(e);
            this.startX = pos.x; this.startY = pos.y;
            this.isDrawing = true;
        },

        onMouseMove(e) {
            if (!this.isDrawing) return;
            const pos = this.getCanvasPos(e);
            this.currentRect = {
                x: Math.min(this.startX, pos.x), y: Math.min(this.startY, pos.y),
                w: Math.abs(pos.x - this.startX), h: Math.abs(pos.y - this.startY),
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

        redraw() {
            const canvas = this.$refs.editorCanvas;
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.drawCheckerboard(ctx, canvas.width, canvas.height);

            if (this.frameImage) {
                ctx.drawImage(this.frameImage, 0, 0, canvas.width, canvas.height);
            }

            this.slots.forEach((slot, i) => {
                ctx.fillStyle = 'rgba(168, 85, 247, 0.25)';
                ctx.fillRect(slot.x, slot.y, slot.w, slot.h);
                ctx.strokeStyle = '#a855f7';
                ctx.lineWidth = 2;
                ctx.strokeRect(slot.x, slot.y, slot.w, slot.h);

                const markerSize = 6;
                ctx.fillStyle = '#a855f7';
                [[slot.x, slot.y], [slot.x+slot.w-markerSize, slot.y],
                 [slot.x, slot.y+slot.h-markerSize], [slot.x+slot.w-markerSize, slot.y+slot.h-markerSize]]
                    .forEach(([mx, my]) => ctx.fillRect(mx, my, markerSize, markerSize));

                const label = `#${i + 1}`;
                const fs    = Math.max(12, Math.min(20, slot.w / 6));
                ctx.font    = `bold ${fs}px Inter, sans-serif`;
                const tw    = ctx.measureText(label).width;
                ctx.fillStyle = 'rgba(88, 28, 135, 0.85)';
                ctx.beginPath();
                ctx.roundRect(slot.x + 4, slot.y + 4, tw + 8, fs + 8, 4);
                ctx.fill();
                ctx.fillStyle = '#ffffff';
                ctx.fillText(label, slot.x + 8, slot.y + 4 + fs);
            });

            if (this.currentRect) {
                ctx.fillStyle = 'rgba(168, 85, 247, 0.15)';
                ctx.fillRect(this.currentRect.x, this.currentRect.y, this.currentRect.w, this.currentRect.h);
                ctx.setLineDash([6, 4]);
                ctx.strokeStyle = '#c084fc';
                ctx.lineWidth = 2;
                ctx.strokeRect(this.currentRect.x, this.currentRect.y, this.currentRect.w, this.currentRect.h);
                ctx.setLineDash([]);
            }
        },

        drawCheckerboard(ctx, w, h) {
            const size = Math.max(8, Math.floor(Math.min(w, h) / 40));
            for (let x = 0; x < w; x += size) {
                for (let y = 0; y < h; y += size) {
                    ctx.fillStyle = ((Math.floor(x/size) + Math.floor(y/size)) % 2 === 0) ? '#2d3748' : '#1a202c';
                    ctx.fillRect(x, y, size, size);
                }
            }
        },

        onFileChange(e) {
            const file = e.target.files[0];
            if (file) this.loadImageFile(file);
        },

        handleFileDrop(e) {
            const file = e.dataTransfer.files[0];
            if (!file || !file.type.includes('png')) { alert('Hanya file PNG.'); return; }
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
                    const maxDim = 1000;
                    const scale  = Math.min(1, maxDim / Math.max(img.width, img.height));
                    this.refW = Math.round(img.width * scale);
                    this.refH = Math.round(img.height * scale);
                    this.resizeCanvas();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        loadExistingFrameImage() {
            if (!this.existingFrameUrl) return;
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => {
                this.frameImage      = img;
                this.frameImageLoaded = true;
                this.redraw();
            };
            img.onerror = () => alert('Gagal memuat gambar frame. Coba upload ulang.');
            img.src = this.existingFrameUrl + '?t=' + Date.now();
        },

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
                    x: Math.round(s.x), y: Math.round(s.y),
                    w: Math.round(s.w), h: Math.round(s.h),
                })),
            };
            document.getElementById('layout-coordinates-input').value = JSON.stringify(payload);
            document.getElementById('slot-count-input').value = this.slots.length || {{ $frame->slot_count }};
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
