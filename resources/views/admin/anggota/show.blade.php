@extends('admin.layouts.app')

@section('title', 'Detail Anggota')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $anggota->nama }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                NIS: {{ $anggota->nis }} | Kelas: {{ $anggota->kelas ?? '-' }}
            </p>
        </div>
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Sedang Dipinjam</h3>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $aktif->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Selesai</h3>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $selesai->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Denda</h3>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $denda->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rating Diberikan</h3>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $ratings->count() }}</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2">
                    <button onclick="showTab('aktif')" id="tab-aktif" class="tab-button active inline-block p-4 border-b-2 border-blue-600 text-blue-600 dark:text-blue-500">
                        Sedang Dipinjam ({{ $aktif->count() }})
                    </button>
                </li>
                <li class="mr-2">
                    <button onclick="showTab('selesai')" id="tab-selesai" class="tab-button inline-block p-4 border-b-2 border-transparent hover:border-gray-300 text-gray-500">
                        Selesai ({{ $selesai->count() }})
                    </button>
                </li>
                <li class="mr-2">
                    <button onclick="showTab('denda')" id="tab-denda" class="tab-button inline-block p-4 border-b-2 border-transparent hover:border-gray-300 text-gray-500">
                        Denda & Telat ({{ $denda->count() }})
                    </button>
                </li>
                <li>
                    <button onclick="showTab('rating')" id="tab-rating" class="tab-button inline-block p-4 border-b-2 border-transparent hover:border-gray-300 text-gray-500">
                        Rating ({{ $ratings->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Aktif -->
        <div id="content-aktif" class="tab-content p-6">
            @if ($aktif->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pinjam</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perpanjangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($aktif as $p)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ $p->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $p->tanggal_pinjam->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $p->jumlah_perpanjangan }}x
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">Tidak ada buku yang sedang dipinjam.</div>
            @endif
        </div>

        <!-- Tab Selesai -->
        <div id="content-selesai" class="tab-content p-6 hidden">
            @if ($selesai->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dikembalikan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($selesai as $p)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ $p->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $p->pengembalian?->tanggal_kembali?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($p->ulasan)
                                            <span class="text-yellow-500">⭐</span> {{ $p->ulasan->rating }}/5
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        Rp {{ number_format($p->denda ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">Tidak ada riwayat selesai.</div>
            @endif
        </div>

        <!-- Tab Denda -->
        <div id="content-denda" class="tab-content p-6 hidden">
            @if ($denda->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dikembalikan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($denda as $p)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <div class="font-medium">{{ $p->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $p->pengembalian?->tanggal_kembali?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        Rp {{ number_format($p->denda, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">Tidak ada denda.</div>
            @endif
        </div>

        <!-- Tab Rating -->
        <div id="content-rating" class="tab-content p-6 hidden">
            @if ($ratings->count() > 0)
                <div class="space-y-4">
                    @foreach ($ratings as $r)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $r->buku->judul }}
                                </div>
                                <div class="flex items-center">
                                    <span class="text-yellow-500 mr-2">⭐</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $r->rating }}/5</span>
                                </div>
                            </div>
                            @if ($r->komentar)
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $r->komentar }}</p>
                            @endif
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                                {{ $r->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">Belum ada rating.</div>
            @endif
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('active', 'border-blue-600', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('content-' + tab).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tab);
    btn.classList.add('active', 'border-blue-600', 'text-blue-600');
    btn.classList.remove('border-transparent', 'text-gray-500');
}
</script>
@endsection
