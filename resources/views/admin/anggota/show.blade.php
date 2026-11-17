@extends('admin.layouts.app')

@section('title', 'Detail Anggota')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $anggota->nama }}
            </h1>
            <p class="text-gray-600 mt-1 text-sm">
                NIS: {{ $anggota->nis }} | Kelas: {{ $anggota->kelas ?? '-' }}
            </p>
        </div>
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-xs font-medium text-gray-500">Sedang Dipinjam</h3>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $aktif->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-xs font-medium text-gray-500">Selesai</h3>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $selesai->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-xs font-medium text-gray-500">Denda</h3>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $denda->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-xs font-medium text-gray-500">Rating Diberikan</h3>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $ratings->count() }}</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 overflow-x-auto">
            <ul class="flex w-full -mb-px text-sm font-medium whitespace-nowrap">
                <li class="flex-1 min-w-0">
                    <button type="button" onclick="showTab('aktif')" id="tab-aktif" class="tab-button active w-full text-center px-3 py-3 border-b-2 border-blue-600 text-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-600">
                        Sedang Dipinjam ({{ $aktif->count() }})
                    </button>
                </li>
                <li class="flex-1 min-w-0">
                    <button type="button" onclick="showTab('selesai')" id="tab-selesai" class="tab-button w-full text-center px-3 py-3 border-b-2 border-transparent hover:border-gray-300 text-gray-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-500">
                        Selesai ({{ $selesai->count() }})
                    </button>
                </li>
                <li class="flex-1 min-w-0">
                    <button type="button" onclick="showTab('denda')" id="tab-denda" class="tab-button w-full text-center px-3 py-3 border-b-2 border-transparent hover:border-gray-300 text-gray-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-500">
                        Denda &amp; Telat ({{ $denda->count() }})
                    </button>
                </li>
                <li class="flex-1 min-w-0">
                    <button type="button" onclick="showTab('rating')" id="tab-rating" class="tab-button w-full text-center px-3 py-3 border-b-2 border-transparent hover:border-gray-300 text-gray-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-500">
                        Rating ({{ $ratings->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Aktif -->
        <div id="content-aktif" class="tab-content p-4">
            @if ($aktif->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pinjam</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perpanjangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($aktif as $p)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="font-medium">{{ $p->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $p->tanggal_pinjam->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $p->jumlah_perpanjangan }}x
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">Tidak ada buku yang sedang dipinjam.</div>
            @endif
        </div>

        <!-- Tab Selesai -->
        <div id="content-selesai" class="tab-content p-4 hidden">
            @if ($selesai->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dikembalikan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ulasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($selesai as $p)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="font-medium">{{ $p->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $p->pengembalian?->tanggal_kembali?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @if ($p->ulasan)
                                            <span class="text-yellow-500">⭐</span> {{ $p->ulasan->rating }}/5
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $p->ulasan?->komentar ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">Tidak ada riwayat selesai.</div>
            @endif
        </div>

        <!-- Tab Denda -->
        <div id="content-denda" class="tab-content p-4 hidden">
            @if ($denda->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($denda as $p)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="font-medium">{{ $p->buku->judul }}</div>
                                        <div class="text-gray-500 text-xs">{{ $p->buku->penulis->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-red-600 font-semibold">
                                        Terlambat {{ (int) $p->tanggal_jatuh_tempo->diffInDays(now()) }} hari
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">Tidak ada denda.</div>
            @endif
        </div>

        <!-- Tab Rating -->
        <div id="content-rating" class="tab-content p-4 hidden">
            @if ($ratings->count() > 0)
                <div class="space-y-4">
                    @foreach ($ratings as $r)
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">
                                    {{ $r->buku->judul }}
                                </div>
                                <div class="flex items-center">
                                    <span class="text-yellow-500 mr-2">⭐</span>
                                    <span class="font-semibold text-gray-900">{{ $r->rating }}/5</span>
                                </div>
                            </div>
                            @if ($r->komentar)
                                <p class="text-gray-600 text-sm">{{ $r->komentar }}</p>
                            @endif
                            <p class="text-gray-400 text-xs mt-1">
                                {{ $r->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">Belum ada rating.</div>
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
