@extends('layouts.admin')

@section('title', 'Manajemen Frame')
@section('page-title', 'Manajemen Frame')
@section('page-subtitle', 'Kelola template frame photobox')

@section('header-actions')
    <a href="{{ route('admin.frames.create') }}"
       class="flex items-center gap-2 bg-purple-600 hover:bg-purple-500 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Frame Baru
    </a>
@endsection

@section('content')
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-gray-800">
        <p class="text-gray-400 text-sm">Total {{ $frames->total() }} frame terdaftar</p>
    </div>

    @if($frames->isEmpty())
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium">Belum ada frame</p>
            <p class="text-gray-600 text-sm mt-1">Klik "Frame Baru" untuk membuat frame pertama</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
            @foreach($frames as $frame)
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-2xl overflow-hidden hover:border-purple-500/30 transition-all duration-200 group">
                <!-- Frame Preview -->
                <div class="aspect-video bg-gray-800 flex items-center justify-center relative overflow-hidden">
                    @if($frame->overlay_image)
                        <img src="{{ Storage::url($frame->overlay_image) }}"
                             alt="{{ $frame->name }}"
                             class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                    @else
                        <!-- Placeholder grid showing slot layout -->
                        <div class="w-full h-full p-4 flex items-center justify-center">
                            @if($frame->slot_count == 1)
                                <div class="w-full h-full bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded-lg border-2 border-dashed border-purple-500/30 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-purple-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @elseif($frame->slot_count == 2)
                                <div class="w-full h-full grid grid-cols-2 gap-1">
                                    @for($i = 0; $i < 2; $i++)
                                    <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded border border-dashed border-purple-500/30"></div>
                                    @endfor
                                </div>
                            @elseif($frame->slot_count == 4)
                                <div class="w-full h-full grid grid-cols-2 gap-1">
                                    @for($i = 0; $i < 4; $i++)
                                    <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded border border-dashed border-purple-500/30"></div>
                                    @endfor
                                </div>
                            @else
                                <div class="w-full h-full grid grid-cols-3 gap-1">
                                    @for($i = 0; $i < 6; $i++)
                                    <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded border border-dashed border-purple-500/30"></div>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Slot Count Badge -->
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-lg">
                        {{ $frame->slot_count }} slot
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-3 left-3">
                        @if($frame->is_active)
                            <span class="flex items-center gap-1 text-xs font-semibold text-green-400 bg-black/60 backdrop-blur-sm px-2.5 py-1 rounded-lg">
                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="text-xs font-semibold text-gray-400 bg-black/60 backdrop-blur-sm px-2.5 py-1 rounded-lg">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-4">
                    <h3 class="font-bold text-white mb-1">{{ $frame->name }}</h3>
                    <p class="text-gray-400 text-xs mb-3 line-clamp-2">{{ $frame->description ?? 'Tidak ada deskripsi' }}</p>

                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-purple-400">
                            Rp {{ number_format($frame->price, 0, ',', '.') }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.frames.edit', $frame) }}"
                               class="p-2 text-gray-400 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.frames.destroy', $frame) }}" method="POST"
                                  onsubmit="return confirm('Hapus frame {{ $frame->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-gray-400 hover:text-red-400 bg-gray-700 hover:bg-red-400/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="px-6 pb-6">
            {{ $frames->links() }}
        </div>
    @endif
</div>
@endsection
