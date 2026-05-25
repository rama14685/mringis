@extends('layouts.app')

@section('title', 'Pilih Frame — Mringis Photobox')

@section('content')
<div class="min-h-screen bg-gray-950 flex flex-col"
     x-data="selectFrame()"
     x-init="startTimer()">

    <!-- Header with Timer -->
    <header class="bg-gray-900/90 backdrop-blur-sm border-b border-gray-800 px-6 py-4 sticky top-0 z-20">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Fase 1</p>
                        <h2 class="text-white font-bold">Pilih Frame</h2>
                    </div>
                </div>
            </div>

            <!-- Token badge -->
            <div class="hidden sm:flex items-center gap-2 bg-gray-800 px-4 py-2 rounded-xl">
                <span class="text-xs text-gray-500">Token:</span>
                <span class="font-mono font-bold text-purple-400 uppercase text-sm">{{ $photoSession->token }}</span>
            </div>

            <!-- Timer -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-gray-500">Waktu tersisa</p>
                    <p class="font-mono font-black text-2xl"
                       :class="timeLeft <= 30 ? 'text-red-400 animate-pulse' : (timeLeft <= 60 ? 'text-yellow-400' : 'text-green-400')"
                       x-text="formatTime(timeLeft)">03:00</p>
                </div>
                <div class="w-12 h-12 relative">
                    <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#374151" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none"
                                :stroke="timeLeft <= 30 ? '#f87171' : (timeLeft <= 60 ? '#fbbf24' : '#34d399')"
                                stroke-width="3"
                                stroke-dasharray="100"
                                :stroke-dashoffset="100 - (timeLeft / 180 * 100)"
                                stroke-linecap="round"
                                style="transition: stroke-dashoffset 1s linear"/>
                    </svg>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-6 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-black text-white mb-2">Pilih Template Frame</h1>
            <p class="text-gray-400">Klik frame yang Anda inginkan, lalu tekan "Lanjut". Timer akan otomatis melanjutkan jika habis.</p>
        </div>

        <!-- Alert flash -->
        @if(session('success'))
            <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-300 px-4 py-3 rounded-xl mb-6 text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Frame Grid -->
        @if($frames->isEmpty())
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-gray-400 font-medium">Belum ada frame tersedia</p>
                <p class="text-gray-600 text-sm mt-1">Hubungi staff untuk bantuan</p>
            </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-24">
            @foreach($frames as $frame)
            <div @click="selectFrame({{ $frame->id }}, {{ $frame->resolved_slot_count }}, '{{ addslashes($frame->name) }}')"
                 :class="selectedFrameId == {{ $frame->id }} ? 'ring-2 ring-purple-500 bg-purple-900/20 border-purple-500/60' : 'border-gray-700/50 hover:border-gray-600'"
                 class="bg-gray-900 border rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:-translate-y-1 hover:shadow-xl hover:shadow-purple-500/10 group">

                <!-- Frame Preview -->
                <div class="aspect-square bg-gray-800 relative p-3">
                    @if($frame->overlay_image)
                        <img src="{{ Storage::url($frame->overlay_image) }}" class="w-full h-full object-contain">
                    @else
                        <!-- Slot layout visual -->
                        <div class="w-full h-full flex items-center justify-center">
                            @if($frame->slot_count == 1)
                                <div class="w-4/5 h-4/5 border-2 border-dashed border-purple-500/40 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    </svg>
                                </div>
                            @elseif($frame->slot_count == 2)
                                <div class="w-full h-full grid grid-cols-2 gap-1.5">
                                    @for($i=0;$i<2;$i++)
                                    <div class="bg-purple-900/30 border border-dashed border-purple-500/30 rounded-lg"></div>
                                    @endfor
                                </div>
                            @elseif($frame->slot_count == 4)
                                <div class="w-full h-full grid grid-cols-2 gap-1.5">
                                    @for($i=0;$i<4;$i++)
                                    <div class="bg-purple-900/30 border border-dashed border-purple-500/30 rounded-lg"></div>
                                    @endfor
                                </div>
                            @else
                                <div class="w-full h-full grid grid-cols-3 gap-1">
                                    @for($i=0;$i<6;$i++)
                                    <div class="bg-purple-900/30 border border-dashed border-purple-500/30 rounded-lg"></div>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Slot badge -->
                    <div class="absolute top-2 right-2 bg-black/70 text-white text-xs font-bold px-2 py-0.5 rounded-lg">
                        {{ $frame->resolved_slot_count }}🖼
                    </div>

                    <!-- Selected checkmark -->
                    <div x-show="selectedFrameId == {{ $frame->id }}"
                         x-transition
                         class="absolute inset-0 bg-purple-500/20 flex items-center justify-center rounded-t-2xl">
                        <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center shadow-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card info (harga disembunyikan karena sudah dibayar sebelum token diterima) -->
                <div class="p-3">
                    <p class="text-sm font-bold text-white truncate">{{ $frame->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $frame->resolved_slot_count }} slot foto</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </main>

    <!-- Bottom CTA Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-gray-900/90 backdrop-blur-sm border-t border-gray-800 px-6 py-4 z-20">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div x-show="selectedFrameId">
                <p class="text-gray-400 text-sm">Frame dipilih:</p>
                <p class="text-white font-bold" x-text="selectedFrameName"></p>
            </div>
            <div x-show="!selectedFrameId" class="text-gray-500 text-sm">
                Pilih frame untuk melanjutkan
            </div>

            <form action="{{ route('photobox.start') }}" method="POST" id="frame-form">
                @csrf
                <input type="hidden" name="frame_id" x-model="selectedFrameId">
                <button type="submit"
                        :disabled="!selectedFrameId"
                        id="lanjut-btn"
                        class="flex items-center gap-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-black text-lg px-8 py-4 rounded-2xl transition-all duration-200 shadow-xl shadow-purple-500/30 hover:-translate-y-0.5">
                    <span>Lanjut ke Sesi Foto</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function selectFrame() {
    return {
        timeLeft: 180, // 3 minutes
        timerInterval: null,
        selectedFrameId: null,
        selectedFrameName: '',
        selectedFramePrice: 0,

        startTimer() {
            this.timerInterval = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(this.timerInterval);
                    this.autoSubmit();
                }
            }, 1000);
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        selectFrame(id, slotCount, name) {
            this.selectedFrameId    = id;
            this.selectedFrameName  = name;
            this.selectedSlotCount  = slotCount;
        },

        autoSubmit() {
            // If timer runs out, submit with whatever frame is selected (or pick first if none)
            const form = document.getElementById('frame-form');
            if (!this.selectedFrameId) {
                // Pick the first available frame
                const firstCard = document.querySelector('[data-frame-id]');
                if (firstCard) {
                    this.selectedFrameId = firstCard.dataset.frameId;
                }
            }
            if (this.selectedFrameId) {
                form.submit();
            }
        }
    };
}
</script>
@endpush
