@extends('admin.layouts.app')

@section('title', 'Detail Buku - ' . $buku->judul)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.buku.index') }}" class="hover:text-cyan-700">Katalog Buku</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Detail</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $buku->judul }}</h1>
        <p class="text-slate-500 mt-1 text-sm">
            {{ $buku->penulis->nama }} <span class="mx-1 text-slate-300">•</span> {{ $buku->penerbit->nama }} ({{ $buku->tahun_terbit }})
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.buku.edit', $buku) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 transition">
            Edit Buku
        </a>
        <a href="{{ route('admin.buku.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>
</div>

<!-- Info Buku -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="md:col-span-1">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center justify-center overflow-hidden">
            @if ($buku->sampul)
                <img src="{{ route('admin.buku.sampul', $buku) }}" alt="{{ $buku->judul }}" class="w-full max-h-96 object-contain rounded-lg">
            @else
                <div class="w-full h-80 bg-slate-100 rounded-lg flex flex-col items-center justify-center text-slate-400">
                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span class="text-sm font-medium">Tidak ada sampul</span>
                </div>
            @endif
        </div>
    </div>
    <div class="md:col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4 border-b border-slate-100 pb-3">Informasi Koleksi Buku</h3>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">ISBN</dt>
                    <dd class="text-sm font-medium text-slate-900 mt-1">{{ $buku->isbn ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</dt>
                    <dd class="text-sm font-medium text-slate-900 mt-1">
                        <span class="px-2 py-0.5 rounded bg-cyan-50 text-cyan-700 text-xs font-bold">{{ $buku->kategori->nama }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Stok</dt>
                    <dd class="text-sm font-bold text-slate-900 mt-1">{{ $buku->stok }} eksemplar</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Tersedia</dt>
                    <dd class="text-sm font-bold {{ $buku->stok_tersedia > 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">{{ $buku->stok_tersedia }} di rak</dd>
                </div>
            </dl>
        </div>

        @if ($buku->deskripsi || $buku->sinopsis)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 mb-2">Deskripsi &amp; Sinopsis</h3>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $buku->deskripsi ?? $buku->sinopsis }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Dipinjam</h3>
        <p class="text-3xl font-bold text-cyan-700 mt-2">{{ $totalPinjam }}</p>
        <p class="text-xs text-slate-400 mt-1">Sepanjang waktu</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Dipinjam</h3>
        <p class="text-3xl font-bold text-amber-600 mt-2">{{ $sedangDipinjam }}</p>
        <p class="text-xs text-slate-400 mt-1">Belum dikembalikan</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rating Rata-rata</h3>
        <p class="text-3xl font-bold text-amber-500 mt-2">
            {{ $rataRating ? number_format($rataRating, 1) : '-' }}
            @if ($rataRating)
                <span class="text-base text-slate-400 font-normal">/ 5</span>
            @endif
        </p>
        <p class="text-xs text-slate-400 mt-1">{{ $ratings->count() }} ulasan siswa</p>
    </div>
</div>

<!-- Riwayat Peminjaman -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800">Riwayat Peminjaman (20 Terakhir)</h2>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">{{ $riwayatPeminjaman->count() }} data</span>
    </div>
    @if ($riwayatPeminjaman->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-6">Nama Peminjam</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">NIS</th>
                        <th class="py-3.5 px-6 whitespace-nowrap">Tanggal Pinjam</th>
                        <th class="py-3.5 px-6 text-right whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($riwayatPeminjaman as $p)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-medium text-slate-800">
                                {{ $p->anggota->nama ?? $p->nama_peminjam }}
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-cyan-800 whitespace-nowrap">{{ $p->nis }}</td>
                            <td class="py-4 px-6 text-slate-600 whitespace-nowrap">
                                {{ $p->tanggal_pinjam->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                @if ($p->status === \App\Models\Peminjaman::STATUS_DIKEMBALIKAN)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Dikembalikan</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12 text-slate-400">Belum ada riwayat peminjaman untuk buku ini.</div>
    @endif
</div>

<!-- Rating & Ulasan -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800">Rating &amp; Ulasan Siswa</h2>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700">{{ $ratings->count() }} ulasan</span>
    </div>
    <div class="p-6">
        @if ($ratings->count() > 0)
            <div class="divide-y divide-slate-100 space-y-4">
                @foreach ($ratings as $r)
                    <div class="pt-4 first:pt-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <div>
                                <div class="font-bold text-slate-800 text-sm">{{ $r->anggota->nama ?? $r->nis }}</div>
                                <div class="text-xs text-slate-400">NIS: {{ $r->nis }}</div>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-amber-500">⭐</span>
                                <span class="font-bold text-slate-800 text-sm">{{ $r->rating }}/5</span>
                            </div>
                        </div>
                        @if ($r->komentar)
                            <p class="text-slate-600 text-sm mt-1 bg-slate-50 p-3 rounded-lg border border-slate-100">{{ $r->komentar }}</p>
                        @endif
                        <p class="text-slate-400 text-xs mt-2">{{ $r->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-slate-400">Belum ada rating atau ulasan untuk buku ini.</div>
        @endif
    </div>
</div>
@endsection