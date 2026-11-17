@extends('admin.layouts.app')

@section('title', 'Detail Anggota - ' . $anggota->nama)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.anggota.index') }}" class="hover:text-cyan-700">Data Siswa / Anggota</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Detail</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">
            {{ $anggota->nama }}
        </h1>
        <p class="text-slate-500 mt-1 text-sm">
            NIS: <span class="font-mono font-semibold text-cyan-800">{{ $anggota->nis }}</span> 
            <span class="mx-1 text-slate-300">•</span> 
            Kelas: <span class="font-medium text-slate-700">{{ $anggota->kelas ?? '-' }}</span>
            @if($anggota->telepon)
                <span class="mx-1 text-slate-300">•</span>
                Telp: <span class="text-slate-700">{{ $anggota->telepon }}</span>
            @endif
        </p>
    </div>
    <div>
        <a href="{{ route('admin.anggota.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition w-full sm:w-auto">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>
</div>

<!-- 4 Kotak Metrik 1x4 (Berfungsi Sekaligus Sebagai Tab Interaktif) -->
<div class="grid grid-cols-4 gap-2 sm:gap-4 mb-6">
    <button type="button" onclick="showTab('aktif')" id="card-aktif" class="stat-card active text-left bg-cyan-50/40 rounded-xl border-2 border-cyan-600 p-2.5 sm:p-4 shadow-sm transition hover:shadow-md focus:outline-none">
        <h3 class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Sedang Dipinjam</h3>
        <p class="text-lg sm:text-3xl font-bold text-cyan-700 mt-1 sm:mt-2">{{ $aktif->count() }}</p>
        <p class="text-[10px] text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Buku aktif</p>
    </button>
    <button type="button" onclick="showTab('selesai')" id="card-selesai" class="stat-card text-left bg-white rounded-xl border border-slate-200 p-2.5 sm:p-4 shadow-sm transition hover:shadow-md focus:outline-none">
        <h3 class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Selesai</h3>
        <p class="text-lg sm:text-3xl font-bold text-emerald-600 mt-1 sm:mt-2">{{ $selesai->count() }}</p>
        <p class="text-[10px] text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Dikembalikan</p>
    </button>
    <button type="button" onclick="showTab('denda')" id="card-denda" class="stat-card text-left bg-white rounded-xl border border-slate-200 p-2.5 sm:p-4 shadow-sm transition hover:shadow-md focus:outline-none">
        <h3 class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Denda &amp; Telat</h3>
        <p class="text-lg sm:text-3xl font-bold text-rose-600 mt-1 sm:mt-2">{{ $denda->count() }}</p>
        <p class="text-[10px] text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Lewat tempo</p>
    </button>
    <button type="button" onclick="showTab('rating')" id="card-rating" class="stat-card text-left bg-white rounded-xl border border-slate-200 p-2.5 sm:p-4 shadow-sm transition hover:shadow-md focus:outline-none">
        <h3 class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Rating Diberikan</h3>
        <p class="text-lg sm:text-3xl font-bold text-amber-500 mt-1 sm:mt-2">{{ $ratings->count() }}</p>
        <p class="text-[10px] text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Ulasan siswa</p>
    </button>
</div>

<!-- Konten Tabel Data Rata Kanan-Kiri -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- Tab Sedang Dipinjam -->
    <div id="content-aktif" class="tab-content">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Daftar Buku Sedang Dipinjam</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-700">{{ $aktif->count() }} buku</span>
        </div>
        @if ($aktif->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm text-slate-600">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3.5 px-6">Buku</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Tanggal Pinjam</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Jatuh Tempo</th>
                            <th class="py-3.5 px-6 text-right whitespace-nowrap">Perpanjangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($aktif as $p)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6 text-slate-900">
                                    <div class="font-bold text-slate-800 text-sm">{{ $p->buku->judul }}</div>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap text-sm">
                                    {{ $p->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap text-sm">
                                    <span class="{{ $p->tanggal_jatuh_tempo->isPast() ? 'text-rose-600 font-semibold' : '' }}">
                                        {{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $p->jumlah_perpanjangan }}x
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-slate-400">
                <p class="text-sm font-medium">Tidak ada buku yang sedang dipinjam.</p>
                <p class="text-xs text-slate-400 mt-1">Saat siswa meminjam buku, catatannya akan muncul di sini.</p>
            </div>
        @endif
    </div>

    <!-- Tab Selesai -->
    <div id="content-selesai" class="tab-content hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Riwayat Peminjaman Selesai</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">{{ $selesai->count() }} selesai</span>
        </div>
        @if ($selesai->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm text-slate-600">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3.5 px-6">Buku</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Tanggal Pinjam</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Dikembalikan</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Rating</th>
                            <th class="py-3.5 px-6 text-right">Ulasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($selesai as $p)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6 text-slate-900">
                                    <div class="font-bold text-slate-800 text-sm">{{ $p->buku->judul }}</div>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap text-sm">
                                    {{ $p->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap text-sm">
                                    {{ $p->pengembalian?->tanggal_kembali?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm">
                                    @if ($p->ulasan)
                                        <span class="inline-flex items-center gap-1 font-semibold text-amber-500">
                                            <span>⭐</span> {{ $p->ulasan->rating }}/5
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right text-sm text-slate-600">
                                    {{ $p->ulasan?->komentar ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-slate-400">
                <p class="text-sm font-medium">Tidak ada riwayat peminjaman selesai.</p>
                <p class="text-xs text-slate-400 mt-1">Buku yang telah dikembalikan akan tercatat di tab ini.</p>
            </div>
        @endif
    </div>

    <!-- Tab Denda -->
    <div id="content-denda" class="tab-content hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Denda &amp; Keterlambatan</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700">{{ $denda->count() }} kasus</span>
        </div>
        @if ($denda->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm text-slate-600">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3.5 px-6">Buku</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Tanggal Pinjam</th>
                            <th class="py-3.5 px-6 whitespace-nowrap">Jatuh Tempo</th>
                            <th class="py-3.5 px-6 text-right whitespace-nowrap">Status Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($denda as $p)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6 text-slate-900">
                                    <div class="font-bold text-slate-800 text-sm">{{ $p->buku->judul }}</div>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap text-sm">
                                    {{ $p->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap text-sm">
                                    {{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                        Terlambat {{ (int) $p->tanggal_jatuh_tempo->diffInDays(now()) }} hari
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-slate-400">
                <p class="text-sm font-medium">Tidak ada data denda atau keterlambatan.</p>
                <p class="text-xs text-slate-400 mt-1">Siswa ini tidak memiliki pinjaman yang melewati batas jatuh tempo.</p>
            </div>
        @endif
    </div>

    <!-- Tab Rating -->
    <div id="content-rating" class="tab-content hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Rating &amp; Ulasan Buku</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700">{{ $ratings->count() }} ulasan</span>
        </div>
        @if ($ratings->count() > 0)
            <div class="p-6 divide-y divide-slate-100 space-y-4">
                @foreach ($ratings as $r)
                    <div class="pt-4 first:pt-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="font-bold text-slate-800 text-sm">
                                {{ $r->buku->judul }}
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-amber-500">⭐</span>
                                <span class="font-bold text-slate-800 text-sm">{{ $r->rating }}/5</span>
                            </div>
                        </div>
                        @if ($r->komentar)
                            <p class="text-slate-600 text-sm mt-1 bg-slate-50 p-3 rounded-lg border border-slate-100">{{ $r->komentar }}</p>
                        @endif
                        <p class="text-slate-400 text-xs mt-2">
                            {{ $r->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-slate-400">
                <p class="text-sm font-medium">Belum ada rating yang diberikan.</p>
                <p class="text-xs text-slate-400 mt-1">Ulasan buku yang diberikan siswa akan tercatat di sini.</p>
            </div>
        @endif
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.stat-card').forEach(el => {
        el.classList.remove('border-cyan-600', 'bg-cyan-50/40', 'border-2');
        el.classList.add('border-slate-200', 'bg-white', 'border');
    });
    
    const content = document.getElementById('content-' + tab);
    if (content) content.classList.remove('hidden');
    
    const card = document.getElementById('card-' + tab);
    if (card) {
        card.classList.remove('border-slate-200', 'bg-white', 'border');
        card.classList.add('border-cyan-600', 'bg-cyan-50/40', 'border-2');
    }
}
</script>
@endsection
