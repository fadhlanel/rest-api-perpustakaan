@extends('admin.layouts.app')

@section('title', 'Detail Buku')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $buku->judul }}</h1>
            <p class="text-gray-600 mt-2">
                {{ $buku->penulis->nama }} | {{ $buku->penerbit->nama }} ({{ $buku->tahun_terbit }})
            </p>
        </div>
        <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    <!-- Info Buku -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="md:col-span-1">
            @if ($buku->sampul)
                <img src="{{ route('admin.buku.sampul', $buku) }}" alt="{{ $buku->judul }}" class="w-full rounded-lg shadow">
            @else
                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400">Tidak ada sampul</span>
                </div>
            @endif
        </div>
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Buku</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                        <dd class="text-sm text-gray-900">{{ $buku->isbn ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                        <dd class="text-sm text-gray-900">{{ $buku->kategori->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stok</dt>
                        <dd class="text-sm text-gray-900">{{ $buku->stok }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tersedia</dt>
                        <dd class="text-sm text-gray-900">{{ $buku->stok_tersedia }}</dd>
                    </div>
                </dl>
            </div>

            @if ($buku->deskripsi || $buku->sinopsis)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Deskripsi</h3>
                    <p class="text-sm text-gray-600">{{ $buku->deskripsi ?? $buku->sinopsis }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Total Dipinjam</h3>
            <p class="text-3xl font-bold text-blue-600  mt-2">{{ $totalPinjam }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Sedang Dipinjam</h3>
            <p class="text-3xl font-bold text-orange-600  mt-2">{{ $sedangDipinjam }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Rating Rata-rata</h3>
            <p class="text-3xl font-bold text-yellow-600  mt-2">
                {{ $rataRating ? number_format($rataRating, 1) : '-' }}
                @if ($rataRating)
                    <span class="text-base">/5</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Ratings -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Rating & Ulasan ({{ $ratings->count() }})</h2>
        </div>
        <div class="p-6">
            @if ($ratings->count() > 0)
                <div class="space-y-4">
                    @foreach ($ratings as $r)
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $r->anggota->nama ?? $r->nis }}</div>
                                    <div class="text-xs text-gray-500">NIS: {{ $r->nis }}</div>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-yellow-500 mr-2">⭐</span>
                                    <span class="font-semibold text-gray-900">{{ $r->rating }}/5</span>
                                </div>
                            </div>
                            @if ($r->komentar)
                                <p class="text-gray-600 text-sm">{{ $r->komentar }}</p>
                            @endif
                            <p class="text-gray-400 text-xs mt-1">{{ $r->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">Belum ada rating.</div>
            @endif
        </div>
    </div>

    <!-- Riwayat Peminjaman -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Riwayat Peminjaman (20 Terakhir)</h2>
        </div>
        <div class="p-6">
            @if ($riwayatPeminjaman->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 ">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pinjam</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 ">
                            @foreach ($riwayatPeminjaman as $p)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $p->anggota->nama ?? $p->nama_peminjam }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->nis }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $p->tanggal_pinjam->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($p->status === \App\Models\Peminjaman::STATUS_DIKEMBALIKAN)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 ">Dikembalikan</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 ">Dipinjam</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">Belum ada riwayat peminjaman.</div>
            @endif
        </div>
    </div>
</div>
@endsection
