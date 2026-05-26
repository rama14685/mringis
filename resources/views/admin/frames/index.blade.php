@extends('layouts.admin')

@section('title', 'Manajemen Frame')
@section('page-title', 'Manajemen Frame')
@section('page-subtitle', 'Kelola template frame photobox')

@section('header-actions')
    <a href="{{ route('admin.frames.create') }}"
       class="flex items-center gap-2 bg-retro-primary border-2 border-retro-text text-white text-sm font-black px-4 py-2.5 rounded-xl shadow-[2px_2px_0px_#202020] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all">
        <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Frame Baru
    </a>
@endsection

@section('content')
<div class="bg-white border-4 border-retro-text rounded-2xl overflow-hidden shadow-[4px_4px_0px_#202020] text-retro-text">
    <div class="p-6 border-b-4 border-retro-text bg-retro-bg/40">
        <p class="text-retro-text font-black text-sm">Total {{ $frames->total() }} frame terdaftar</p>
    </div>

    @if($frames->isEmpty())
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-retro-bg border-2 border-retro-text rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-[2px_2px_0px_#202020]">
                <svg class="w-8 h-8 text-retro-text/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-retro-text/80 font-black">Belum ada frame</p>
            <p class="text-retro-text/60 text-xs mt-1">Klik "Frame Baru" untuk membuat frame pertama</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
            @foreach($frames as $frame)
            <div class="bg-white border-4 border-retro-text rounded-2xl overflow-hidden shadow-[4px_4px_0px_#202020] transition-all duration-200 hover:-translate-y-1 hover:shadow-[6px_6px_0px_#202020] group">
                <!-- Frame Preview -->
                <div class="aspect-video bg-retro-bg border-b-4 border-retro-text flex items-center justify-center relative overflow-hidden">
                    @if($frame->overlay_image)
                        <img src="{{ Storage::url($frame->overlay_image) }}"
                             alt="{{ $frame->name }}"
                             class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity">
                    @else
                        <!-- Placeholder grid showing slot layout -->
                        <div class="w-full h-full p-4 flex items-center justify-center">
                            @if($frame->slot_count == 1)
                                <div class="w-full h-full bg-white rounded-lg border-2 border-dashed border-retro-text flex items-center justify-center">
                                    <svg class="w-8 h-8 text-retro-text/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @elseif($frame->slot_count == 2)
                                <div class="w-full h-full grid grid-cols-2 gap-1">
                                    @for($i = 0; $i < 2; $i++)
                                    <div class="bg-white rounded border border-dashed border-retro-text/30"></div>
                                    @endfor
                                </div>
                            @elseif($frame->slot_count == 4)
                                <div class="w-full h-full grid grid-cols-2 gap-1">
                                    @for($i = 0; $i < 4; $i++)
                                    <div class="bg-white rounded border border-dashed border-retro-text/30"></div>
                                    @endfor
                                </div>
                            @else
                                <div class="w-full h-full grid grid-cols-3 gap-1">
                                    @for($i = 0; $i < 6; $i++)
                                    <div class="bg-white rounded border border-dashed border-retro-text/30"></div>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Slot Count Badge -->
                    <div class="absolute top-3 right-3 bg-retro-bg border-2 border-retro-text text-retro-text text-xs font-black px-2.5 py-1 rounded shadow-[2px_2px_0px_#202020]">
                        {{ $frame->slot_count }} slot
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-3 left-3">
                        @if($frame->is_active)
                            <span class="flex items-center gap-1.5 text-xs font-black text-white bg-retro-accent2 border-2 border-retro-text px-2.5 py-1 rounded shadow-[2px_2px_0px_#202020]">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="text-xs font-black text-retro-text/60 bg-white border-2 border-retro-text px-2.5 py-1 rounded">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-4 bg-white">
                    <h3 class="font-black text-retro-text text-base mb-1">{{ $frame->name }}</h3>
                    <p class="text-retro-text/60 text-xs mb-3 line-clamp-2 font-semibold">{{ $frame->description ?? 'Tidak ada deskripsi' }}</p>

                    <div class="flex items-center justify-between">
                        <span class="text-lg font-black text-retro-accent1">
                            Rp {{ number_format($frame->price, 0, ',', '.') }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.frames.edit', $frame) }}"
                               class="p-2 text-retro-text hover:text-white bg-white hover:bg-retro-accent2 border-2 border-retro-text rounded-xl shadow-[2px_2px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all cursor-pointer">
                                <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.frames.destroy', $frame) }}" method="POST"
                                  onsubmit="return confirm('Hapus frame {{ $frame->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-retro-text hover:text-white bg-white hover:bg-retro-primary border-2 border-retro-text rounded-xl shadow-[2px_2px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all cursor-pointer">
                                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="px-6 pb-6 bg-retro-bg/10 border-t-2 border-retro-text">
            {{ $frames->links() }}
        </div>
    @endif
</div>
@endsection
