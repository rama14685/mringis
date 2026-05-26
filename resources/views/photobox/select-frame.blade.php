@extends('layouts.app')

@section('title', 'Pilih Frame — Mringis Photobox')

@section('content')
<div class="min-h-screen bg-retro-bg text-retro-text flex flex-col"
     x-data="selectFrame()"
     x-init="startTimer()">

    <!-- Header with Timer -->
    <header class="bg-white border-b-4 border-retro-text px-6 py-4 sticky top-0 z-20">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-retro-accent1 border-2 border-retro-text rounded-xl flex items-center justify-center shadow-[2px_2px_0px_0px_#202020]">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-retro-text/50 font-bold uppercase tracking-wider">Fase 1</p>
                        <h2 class="text-retro-text font-black">Pilih Frame</h2>
                    </div>
                </div>
            </div>

            <!-- Token badge -->
            <div class="hidden sm:flex items-center gap-2 bg-retro-bg border-2 border-retro-text px-4 py-2 rounded-xl">
                <span class="text-xs text-retro-text/50 font-bold">Token:</span>
                <span class="font-mono font-black text-retro-accent1 uppercase text-sm">{{ $photoSession->token }}</span>
            </div>

            <!-- Timer -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-retro-text/50 font-bold">Waktu tersisa</p>
                    <p class="font-mono font-black text-2xl"
                       :class="timeLeft <= 30 ? 'text-retro-accent1 animate-pulse' : (timeLeft <= 60 ? 'text-retro-accent2' : 'text-retro-text')"
                       x-text="formatTime(timeLeft)">03:00</p>
                </div>
                <div class="w-12 h-12 relative">
                    <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#202020" stroke-opacity="0.1" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none"
                                :stroke="timeLeft <= 30 ? '#ee4266' : '#2a9d8f'"
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
            <h1 class="text-3xl font-black text-retro-text mb-2">Pilih Template Frame</h1>
            <p class="text-retro-text/70 font-semibold">Klik frame yang Anda inginkan, lalu tekan "Lanjut". Timer akan otomatis melanjutkan jika habis.</p>
        </div>

        <!-- Alert flash -->
        @if(session('success'))
            <div class="flex items-center gap-3 bg-retro-accent2/10 border-2 border-retro-accent2 text-retro-text px-4 py-3 rounded-xl mb-6 text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Frame Grid -->
        @if($frames->isEmpty())
            <div class="text-center py-20 bg-white border-4 border-retro-text rounded-3xl p-8 shadow-[8px_8px_0px_0px_#202020]">
                <div class="w-20 h-20 bg-retro-bg border-4 border-retro-text rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-[4px_4px_0px_0px_#202020]">
                    <svg class="w-10 h-10 text-retro-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-retro-text font-black text-lg">Belum ada frame tersedia</p>
                <p class="text-retro-text/60 font-semibold mt-1">Hubungi staff untuk bantuan</p>
            </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 mb-28">
            @foreach($frames as $frame)
            <div @click="selectFrame({{ $frame->id }}, {{ $frame->resolved_slot_count }}, '{{ addslashes($frame->name) }}')"
                 :class="selectedFrameId == {{ $frame->id }} ? 'border-retro-accent1 bg-retro-accent1/5 shadow-[4px_4px_0px_0px_#ee4266]' : 'border-retro-text shadow-[4px_4px_0px_0px_#202020] bg-white'"
                 class="border-4 rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:-translate-y-0.5 group">

                <!-- Frame Preview -->
                <div class="aspect-square bg-retro-bg/40 relative p-3 border-b-2 border-retro-text">
                    @if($frame->overlay_image)
                        <img src="{{ Storage::url($frame->overlay_image) }}" class="w-full h-full object-contain">
                    @else
                        <!-- Slot layout visual -->
                        <div class="w-full h-full flex items-center justify-center">
                            @if($frame->slot_count == 1)
                                <div class="w-4/5 h-4/5 border-2 border-dashed border-retro-accent1/40 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-retro-accent1/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    </svg>
                                </div>
                            @elseif($frame->slot_count == 2)
                                <div class="w-full h-full grid grid-cols-2 gap-1.5">
                                    @for($i=0;$i<2;$i++)
                                    <div class="bg-retro-accent2/10 border-2 border-dashed border-retro-accent2/30 rounded-lg"></div>
                                    @endfor
                                </div>
                            @elseif($frame->slot_count == 4)
                                <div class="w-full h-full grid grid-cols-2 gap-1.5">
                                    @for($i=0;$i<4;$i++)
                                    <div class="bg-retro-accent2/10 border-2 border-dashed border-retro-accent2/30 rounded-lg"></div>
                                    @endfor
                                </div>
                            @else
                                <div class="w-full h-full grid grid-cols-3 gap-1">
                                    @for($i=0;$i<6;$i++)
                                    <div class="bg-retro-accent2/10 border-2 border-dashed border-retro-accent2/30 rounded-lg"></div>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Slot badge -->
                    <div class="absolute top-2 right-2 bg-retro-text text-white text-xs font-bold px-2 py-0.5 rounded-lg border-2 border-retro-text shadow-[1px_1px_0px_0px_rgba(255,255,255,0.2)]">
                        {{ $frame->resolved_slot_count }}🖼
                    </div>

                    <!-- Selected checkmark -->
                    <div x-show="selectedFrameId == {{ $frame->id }}"
                         x-transition
                         class="absolute inset-0 bg-retro-accent1/10 flex items-center justify-center">
                        <div class="w-10 h-10 bg-retro-accent1 border-2 border-retro-text text-white rounded-full flex items-center justify-center shadow-[2px_2px_0px_0px_#202020]">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card info -->
                <div class="p-3">
                    <p class="text-sm font-bold text-retro-text truncate">{{ $frame->name }}</p>
                    <p class="text-xs text-retro-text/60 font-semibold mt-0.5">{{ $frame->resolved_slot_count }} slot foto</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </main>

    <!-- Bottom CTA Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t-4 border-retro-text px-6 py-4 z-20">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div x-show="selectedFrameId">
                <p class="text-retro-text/50 text-xs font-bold">Frame dipilih:</p>
                <p class="text-retro-text font-black" x-text="selectedFrameName"></p>
            </div>
            <div x-show="!selectedFrameId" class="text-retro-text/40 text-sm font-bold">
                Pilih frame untuk melanjutkan
            </div>

            <form action="{{ route('photobox.start') }}" method="POST" id="frame-form">
                @csrf
                <input type="hidden" name="frame_id" x-model="selectedFrameId">
                <button type="submit"
                        :disabled="!selectedFrameId"
                        id="lanjut-btn"
                        class="flex items-center gap-3 bg-retro-accent1 border-4 border-retro-text text-white font-black text-lg px-8 py-4 rounded-2xl transition-all duration-200 shadow-[4px_4px_0px_0px_#202020] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#202020] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:opacity-40 disabled:cursor-not-allowed">
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
