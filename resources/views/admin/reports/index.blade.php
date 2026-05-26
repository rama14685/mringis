@extends('layouts.admin')

@section('title', 'Laporan Pendapatan')
@section('page-title', 'Laporan Pendapatan')
@section('page-subtitle', 'Rekap transaksi dan pendapatan photobox')

@section('content')
<div x-data="reports()" class="text-retro-text">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-retro-accent1 border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020] text-white">
            <p class="text-white/80 text-xs font-black mb-2 uppercase tracking-wider">Total Pendapatan</p>
            <p class="text-3xl font-black">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
            <p class="text-retro-text/60 text-xs font-black mb-2 uppercase tracking-wider">Foto Tercetak</p>
            <p class="text-3xl font-black">{{ number_format($totalPrinted) }}</p>
        </div>
        <div class="bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
            <p class="text-retro-text/60 text-xs font-black mb-2 uppercase tracking-wider">Rata-rata per Sesi</p>
            <p class="text-3xl font-black">
                Rp {{ $totalPrinted > 0 ? number_format($totalRevenue / $totalPrinted, 0, ',', '.') : '0' }}
            </p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white border-4 border-retro-text rounded-2xl p-6 mb-6 shadow-[4px_4px_0px_#202020]">
        <h3 class="text-sm font-black text-retro-text mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-retro-text stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter Data
        </h3>
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-xs font-bold text-retro-text block mb-1.5">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full bg-white border-2 border-retro-text rounded-xl px-3 py-2.5 text-retro-text text-sm font-bold focus:outline-none focus:ring-2 focus:ring-retro-primary/50">
            </div>
            <div>
                <label class="text-xs font-bold text-retro-text block mb-1.5">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full bg-white border-2 border-retro-text rounded-xl px-3 py-2.5 text-retro-text text-sm font-bold focus:outline-none focus:ring-2 focus:ring-retro-primary/50">
            </div>
            <div>
                <label class="text-xs font-bold text-retro-text block mb-1.5">Frame</label>
                <select name="frame_id"
                        class="w-full bg-white border-2 border-retro-text rounded-xl px-3 py-2.5 text-retro-text text-sm font-bold focus:outline-none focus:ring-2 focus:ring-retro-primary/50">
                    <option value="">Semua Frame</option>
                    @foreach($frames as $frame)
                        <option value="{{ $frame->id }}" {{ request('frame_id') == $frame->id ? 'selected' : '' }}>
                            {{ $frame->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 bg-retro-primary border-2 border-retro-text text-white text-sm font-black px-4 py-2.5 rounded-xl shadow-[2px_2px_0px_#202020] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.reports.index') }}"
                   class="px-4 py-2.5 bg-white border-2 border-retro-text text-retro-text text-sm font-bold rounded-xl shadow-[2px_2px_0px_#202020] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all cursor-pointer">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Monthly Summary -->
    @if($monthlyStats->isNotEmpty())
    <div class="bg-white border-4 border-retro-text rounded-2xl p-6 mb-6 shadow-[4px_4px_0px_#202020]">
        <h3 class="text-lg font-black text-retro-text mb-4">Ringkasan Bulanan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-4 border-retro-text">
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Bulan</th>
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Sesi</th>
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Pendapatan</th>
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-retro-text/20">
                    @php $maxRevenue = $monthlyStats->max('total_revenue') ?: 1; @endphp
                    @foreach($monthlyStats as $stat)
                    <tr>
                        <td class="py-3 pr-4 font-black text-retro-text">
                            {{ \Carbon\Carbon::createFromDate($stat->year, $stat->month, 1)->translatedFormat('F Y') }}
                        </td>
                        <td class="py-3 pr-4 font-bold text-retro-text/80">{{ number_format($stat->total_sessions) }} sesi</td>
                        <td class="py-3 pr-4 font-black text-retro-accent1">
                            Rp {{ number_format($stat->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="py-3 w-48">
                            <div class="bg-retro-bg border-2 border-retro-text rounded-full h-4 overflow-hidden shadow-[1px_1px_0px_#202020]">
                                <div class="bg-retro-accent2 h-full rounded-full border-r-2 border-retro-text transition-all"
                                     style="width: {{ ($stat->total_revenue / $maxRevenue) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Transactions Table -->
    <div class="bg-white border-4 border-retro-text rounded-2xl overflow-hidden shadow-[4px_4px_0px_#202020]">
        <div class="p-6 border-b-4 border-retro-text flex items-center justify-between">
            <h3 class="text-lg font-black text-retro-text">Detail Transaksi</h3>
            <span class="text-xs font-black text-retro-text bg-retro-bg border-2 border-retro-text px-3 py-1.5 rounded-lg shadow-[2px_2px_0px_#202020]">
                {{ $sessions->total() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-retro-bg border-b-2 border-retro-text">
                    <tr>
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider px-6 py-4">Token</th>
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider px-6 py-4">Frame</th>
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider px-6 py-4">Harga</th>
                        <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider px-6 py-4">Waktu Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-retro-text/20">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-retro-bg/40 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-black text-retro-text bg-retro-bg border-2 border-retro-text px-2.5 py-1 rounded-lg uppercase text-sm shadow-[2px_2px_0px_#202020]">
                                {{ $session->token }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-retro-text font-bold">{{ $session->frame?->name ?? '—' }}</td>
                        <td class="px-6 py-4 font-black text-retro-accent1">
                            Rp {{ number_format($session->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-retro-text/60 text-xs font-semibold">
                            {{ $session->printed_at?->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-retro-text/40 font-bold">
                            Belum ada transaksi yang selesai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
        <div class="p-6 border-t-4 border-retro-text bg-retro-bg/10">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function reports() {
    return {};
}
</script>
@endpush
