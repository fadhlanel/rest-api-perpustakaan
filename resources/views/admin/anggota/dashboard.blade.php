@extends('admin.layouts.app')

@section('title', 'Dashboard Anggota')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Dashboard Anggota: {{ $anggota->nama }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                NIS: {{ $anggota->nis }} | Kelas: {{ $anggota->kelas ?? '-' }}
            </p>
        </div>
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Peminjaman Aktif</h3>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                {{ $peminjamanAktif->count() }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Buku sedang dipinjam
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Denda</h3>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">
                Rp {{ number_format($totalDenda, 0, ',', '.') }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Denda dari rating buku
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ulasan</h3>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                {{ $ulasanTersedia->count() }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Ulasan/book yang di-rating
            </p>
        </div>
    </div>

    <!-- Active Loans -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Buku Sedang Dipinjam
            </h2>
        </div>
        <div class="p-6">
            @if ($peminjamanAktif->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Buku
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Pinjam
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jatuh Tempo
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Perpanjangan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($peminjamanAktif as $peminjaman)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ $peminjaman->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">
                                            {{ $peminjaman->buku->penulis ?? '-' }} | {{ $peminjaman->buku->penerbit ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $peminjaman->jumlah_perpanjangan }}x
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Tidak ada buku yang sedang dipinjam.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Returned Books -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Riwayat Peminjaman Selesai
            </h2>
        </div>
        <div class="p-6">
            @if ($peminjamanDikembalikan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Buku
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Kembali
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rating
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Denda
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ulasan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($peminjamanDikembalikan as $peminjaman)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ $peminjaman->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">
                                            {{ $peminjaman->buku->penulis ?? '-' }} | {{ $peminjaman->buku->penerbit ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($peminjaman->pengembalian)
                                            {{ \Carbon\Carbon::parse($peminjaman->pengembalian->tanggal_kembali)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($peminjaman->ulasan)
                                            {{ $peminjaman->ulasan->rating }}/5
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($peminjaman->ulasan && $peminjaman->ulasan->denda)
                                            Rp {{ number_format($peminjaman->ulasan->denda, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($peminjaman->ulasan && $peminjaman->ulasan->ulasan)
                                            {{ Str::limit($peminjaman->ulasan->ulasan, 50) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Tidak ada riwayat peminjaman selesai.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Ratings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Ulasan yang Diberikan
            </h2>
        </div>
        <div class="p-6">
            @if ($ulasanTersedia->count() > 0)
                <div class="space-y-4">
                    @foreach ($ulasanTersedia as $ulasan)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                            <div class="flex items-center justify-between">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $ulasan->buku->judul ?? 'Buku Tidak Diketahui' }}
                                </div>
                                <div class="flex items-center">
                                    <span class="text-yellow-500 mr-2">⭐</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $ulasan->rating }}/5
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm">
                                {{ $ulasan->ulasan ?? '-' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada ulasan yang diberikan.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
