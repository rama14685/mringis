@extends('layouts.admin')

@section('title', 'Laporan Pendapatan')
@section('page-title', 'Laporan Pendapatan')
@section('page-subtitle', 'Rekap transaksi dan pendapatan photobox')

@section('content')
<div x-data="reports()">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl p-6 shadow-xl shadow-purple-500/20">
            <p class="text-purple-200 text-sm font-medium mb-2">Total Pendapatan</p>
            <p class="text-3xl font-black text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
            <p class="text-gray-400 text-sm mb-2">Foto Tercetak</p>
            <p class="text-3xl font-black text-white">{{ number_format($totalPrinted) }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
            <p class="text-gray-400 text-sm mb-2">Rata-rata per Sesi</p>
            <p class="text-3xl font-black text-white">
                Rp {{ $totalPrinted > 0 ? number_format($totalRevenue / $totalPrinted, 0, ',', '.') : '0' }}
            </p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter Data
        </h3>
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-xs text-gray-400 block mb-1.5">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50">
            </div>
            <div>
                <label class="text-xs text-gray-400 block mb-1.5">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50">
            </div>
            <div>
                <label class="text-xs text-gray-400 block mb-1.5">Frame</label>
                <select name="frame_id"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50">
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
                        class="flex-1 bg-purple-600 hover:bg-purple-500 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.reports.index') }}"
                   class="px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-xl transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Monthly Summary -->
    @if($monthlyStats->isNotEmpty())
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-white mb-4">Ringkasan Bulanan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Bulan</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Sesi</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Pendapatan</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxRevenue = $monthlyStats->max('total_revenue') ?: 1; @endphp
                    @foreach($monthlyStats as $stat)
                    <tr class="border-b border-gray-800/50">
                        <td class="py-3 pr-4 font-medium text-white">
                            {{ \Carbon\Carbon::createFromDate($stat->year, $stat->month, 1)->translatedFormat('F Y') }}
                        </td>
                        <td class="py-3 pr-4 text-gray-300">{{ number_format($stat->total_sessions) }} sesi</td>
                        <td class="py-3 pr-4 font-bold text-purple-400">
                            Rp {{ number_format($stat->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="py-3 w-48">
                            <div class="bg-gray-800 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full transition-all"
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
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Detail Transaksi</h3>
            <span class="text-xs text-gray-500 bg-gray-800 px-3 py-1.5 rounded-lg">
                {{ $sessions->total() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-4">Token</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-4">Frame</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-4">Harga</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-6 py-4">Waktu Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-white bg-gray-800 px-2.5 py-1 rounded-lg uppercase text-sm">
                                {{ $session->token }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $session->frame?->name ?? '—' }}</td>
                        <td class="px-6 py-4 font-bold text-purple-400">
                            Rp {{ number_format($session->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">
                            {{ $session->printed_at?->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                            Belum ada transaksi yang selesai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
        <div class="p-6 border-t border-gray-800">
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
