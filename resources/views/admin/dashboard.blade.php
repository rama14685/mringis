@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di panel admin Mringis Photobox')

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
<div x-data="dashboard()" x-init="init()">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8 text-retro-text">
        <!-- Total Sessions -->
        <div class="bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-retro-accent1/10 border-2 border-retro-text rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-retro-accent1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                </div>
                <span class="text-xs font-black text-retro-text bg-retro-bg border-2 border-retro-text px-2 py-1 rounded-lg">Total</span>
            </div>
            <p class="text-3xl font-black text-retro-text">{{ number_format($totalSessions) }}</p>
            <p class="text-xs text-retro-text/60 font-bold mt-1">Total Sesi Dibuat</p>
        </div>

        <!-- Active Tokens -->
        <div class="bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-retro-accent2/10 border-2 border-retro-text rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-retro-accent2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-black text-white bg-retro-accent2 border-2 border-retro-text px-2 py-1 rounded-lg shadow-[1px_1px_0px_#202020]">Aktif</span>
            </div>
            <p class="text-3xl font-black text-retro-text">{{ number_format($activeSessions) }}</p>
            <p class="text-xs text-retro-text/60 font-bold mt-1">Token Aktif</p>
        </div>

        <!-- Printed -->
        <div class="bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-retro-accent2/10 border-2 border-retro-text rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-retro-accent2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                <span class="text-xs font-black text-retro-accent2 bg-retro-accent2/10 border-2 border-retro-text px-2 py-1 rounded-lg">Selesai</span>
            </div>
            <p class="text-3xl font-black text-retro-text">{{ number_format($printedSessions) }}</p>
            <p class="text-xs text-retro-text/60 font-bold mt-1">Foto Tercetak</p>
        </div>

        <!-- Revenue -->
        <div class="bg-retro-accent1 border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020] text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 border-2 border-white rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-black text-retro-text bg-white border-2 border-retro-text px-2 py-1 rounded-lg shadow-[1px_1px_0px_#202020]">IDR</span>
            </div>
            <p class="text-3xl font-black">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-white/80 font-bold mt-1">Total Pendapatan</p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 text-retro-text">

        <!-- Generate Token Card -->
        <div class="xl:col-span-1 bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black">Generate Token</h3>
                <div class="w-8 h-8 bg-retro-accent1/10 border-2 border-retro-text rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-retro-accent1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </div>

            <!-- Harga Sesi -->
            <div class="mb-4">
                <label class="text-sm font-bold block mb-2">Harga Sesi</label>
                <div class="flex items-center gap-2 bg-retro-bg border-2 border-retro-text rounded-xl px-4 py-3">
                    <span class="text-retro-text/60 text-sm font-bold">Rp</span>
                    <input type="number" x-model="tokenPrice" min="0" step="1000"
                           class="flex-1 bg-transparent text-retro-text font-black focus:outline-none text-sm"
                           placeholder="10000">
                </div>
            </div>

            <!-- Token Display -->
            <div x-show="generatedToken"
                 x-transition
                 class="mb-4 bg-retro-bg border-4 border-retro-text rounded-xl p-4 text-center shadow-[4px_4px_0px_#202020]">
                <p class="text-xs text-retro-text/60 font-bold mb-2">Token Baru Berhasil Dibuat:</p>
                <p class="text-4xl font-black tracking-[0.2em] text-retro-accent1 uppercase" x-text="generatedToken"></p>
                <p class="text-xs text-retro-accent2 font-bold mt-2">✓ Siap digunakan</p>
            </div>

            <button @click="generateToken()"
                    id="generate-token-btn"
                    :disabled="isGenerating"
                    class="w-full flex items-center justify-center gap-3 bg-retro-primary border-4 border-retro-text text-white font-black py-4 rounded-xl transition-all duration-200 shadow-[4px_4px_0px_#202020] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_#202020] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none cursor-pointer">
                <svg x-show="!isGenerating" class="w-5 h-5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg x-show="isGenerating" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" style="display:none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="isGenerating ? 'Membuat Token...' : 'Generate Token Sesi'"></span>
            </button>

            <p class="text-xs text-retro-text/40 text-center font-semibold mt-3">Token berisi 5 karakter acak (huruf + angka)</p>
        </div>

        <!-- Recent Sessions Table -->
        <div class="xl:col-span-2 bg-white border-4 border-retro-text rounded-2xl p-6 shadow-[4px_4px_0px_#202020]">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black">Sesi Terbaru</h3>
                <a href="{{ route('admin.reports.index') }}"
                   class="text-xs text-retro-primary hover:underline transition-colors font-black uppercase tracking-wider">
                    Lihat semua →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-4 border-retro-text">
                            <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Token</th>
                            <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Frame</th>
                            <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Status</th>
                            <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Harga</th>
                            <th class="text-left text-xs font-black text-retro-text/60 uppercase tracking-wider pb-3">Dibuat</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-retro-text/20" id="sessions-tbody">
                        @forelse($recentSessions as $session)
                        <tr class="hover:bg-retro-bg/40 transition-colors" id="session-row-{{ $session->id }}">
                            <td class="py-3.5 pr-4">
                                <span class="font-mono font-black text-retro-text tracking-wider uppercase bg-retro-bg border-2 border-retro-text px-2.5 py-1 rounded-lg text-sm shadow-[2px_2px_0px_#202020]">
                                    {{ $session->token }}
                                </span>
                            </td>
                            <td class="py-3.5 pr-4">
                                <span class="font-bold">{{ $session->frame?->name ?? '—' }}</span>
                            </td>
                            <td class="py-3.5 pr-4">
                                @if($session->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-black text-white bg-retro-accent2 border-2 border-retro-text px-2.5 py-1 rounded-full shadow-[2px_2px_0px_#202020]">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                        Aktif
                                    </span>
                                @elseif($session->status === 'used')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-retro-text bg-retro-bg border-2 border-retro-text px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-retro-text/40 rounded-full"></span>
                                        Terpakai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-retro-text/60 bg-retro-bg border border-dashed border-retro-text/30 px-2.5 py-1 rounded-full">
                                        Kedaluwarsa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 pr-4 font-black">
                                Rp {{ number_format($session->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 pr-4 text-retro-text/60 text-xs font-semibold">
                                {{ $session->created_at->diffForHumans() }}
                            </td>
                            <td class="py-3.5">
                                @if($session->status === 'active')
                                <form action="{{ route('admin.expire-token', $session) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs font-bold text-white bg-retro-primary border-2 border-retro-text px-2.5 py-1 rounded-lg shadow-[2px_2px_0px_#202020] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_#202020] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none cursor-pointer"
                                            onclick="return confirm('Nonaktifkan token ini?')">
                                        Nonaktifkan
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-retro-text/40 font-bold">
                                Belum ada sesi yang dibuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        isGenerating: false,
        generatedToken: null,
        tokenPrice: 10000,

        init() {},

        async generateToken() {
            this.isGenerating = true;
            this.generatedToken = null;

            try {
                const response = await fetch('{{ route('admin.generate-token') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ price: this.tokenPrice }),
                });

                const data = await response.json();

                if (data.success) {
                    this.generatedToken = data.token.toUpperCase();

                    // Add new row to table
                    const tbody = document.getElementById('sessions-tbody');
                    const emptyRow = tbody.querySelector('td[colspan]');
                    if (emptyRow) emptyRow.closest('tr').remove();

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-retro-bg/40 transition-colors bg-retro-secondary/5';
                    tr.id = 'session-row-' + data.session_id;
                    tr.innerHTML = `
                        <td class="py-3.5 pr-4">
                            <span class="font-mono font-black text-retro-text tracking-wider uppercase bg-retro-bg border-2 border-retro-text px-2.5 py-1 rounded-lg text-sm shadow-[2px_2px_0px_#202020]">
                                ${data.token.toUpperCase()}
                            </span>
                        </td>
                        <td class="py-3.5 pr-4"><span class="font-bold">—</span></td>
                        <td class="py-3.5 pr-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-black text-white bg-retro-accent2 border-2 border-retro-text px-2.5 py-1 rounded-full shadow-[2px_2px_0px_#202020]">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>Aktif
                            </span>
                        </td>
                        <td class="py-3.5 pr-4 font-black">Rp ${Number(this.tokenPrice).toLocaleString('id-ID')}</td>
                        <td class="py-3.5 pr-4 text-retro-text/60 text-xs font-semibold">Baru saja</td>
                        <td class="py-3.5"></td>
                    `;
                    tbody.prepend(tr);
                } else {
                    alert('Gagal membuat token. Silakan coba lagi.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.isGenerating = false;
            }
        }
    };
}
</script>
@endpush
