@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <nav class="flex items-center text-xs text-slate-500 uppercase tracking-wider">
        <p class="font-semibold">Katalog & Master Data</p>
        <svg class="w-3 h-3 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <p class="font-semibold text-slate-800">Koleksi Induk Pustaka</p>
    </nav>
    <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Master Data & Katalog Buku</h1>
    <p class="mt-2 text-sm text-slate-600">Kelola koleksi pustaka, metadata buku, kategori, penerbit, serta sinkronisasi ketersediaan untuk aplikasi mobile siswa.</p>
</div>

<div class="flex flex-wrap gap-3 mb-7">
    <button class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Import CSV
    </button>
    <button class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-12a2 2 0 00-2-2h-2M8 5a2 2 0 002 2h2a2 2 0 002-2m0 0a2 2 0 012 2v2a2 2 0 01-2 2m-6-6a2 2 0 00-2 2v2a2 2 0 002 2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z"></path></svg>
        Export Label / PDF
    </button>
    <a href="{{ route('admin.buku.create') }}" class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-blue-800 rounded-lg shadow-sm hover:bg-blue-900 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Koleksi Buku
    </a>
</div>

<div class="grid grid-cols-1 gap-5 mb-7 xl:grid-cols-3">
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Total Judul Terdaftar</p>
        <p class="text-3xl font-bold tabular-nums text-slate-950">{{ number_format($stats['total_buku']) }}</p>
        <p class="mt-2 text-xs text-slate-500">{{ number_format($stats['total_eksemplar']) }} eksemplar di rak</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Total Eksemplar Fisik</p>
        <p class="text-3xl font-bold tabular-nums text-slate-950">{{ number_format($stats['total_eksemplar']) }}</p>
        <p class="mt-2 text-xs text-emerald-600 flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            {{ number_format($stats['tersedia']) }} tersedia di rak
        </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Sedang Dipinjam Siswa</p>
        <p class="text-3xl font-bold tabular-nums text-slate-950">{{ number_format($stats['dipinjam']) }}</p>
        <p class="mt-2 text-xs text-orange-600 flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-orange-600"></span>
            Peminjaman aktif
        </p>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div class="relative w-full max-w-md">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" id="dashboardSearch" name="search" value="{{ request('search') }}" placeholder="Cari judul, ISBN, penulis, penerbit..." class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
        </div>
        <div class="flex gap-2">
            <select id="kategoriFilter" name="kategori_id" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-sky-500">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $item)<option value="{{ $item->id }}" @selected(request('kategori_id') == $item->id)>{{ $item->nama }}</option>@endforeach
            </select>
            <select id="statusFilter" name="status_ketersediaan" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-sky-500">
                <option value="">Status Ketersediaan</option>
                <option value="tersedia" @selected(request('status_ketersediaan') === 'tersedia')>Tersedia</option>
                <option value="habis" @selected(request('status_ketersediaan') === 'habis')>Habis</option>
            </select>
            <button type="button" onclick="resetDashboardSearch()" class="px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white hover:bg-slate-50 transition">
                Reset
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Cover & Informasi Buku</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori Dewey</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Penulis & Penerbit</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Stok Koleksi</th>
                </tr>
            </thead>
            <tbody id="dashboardTableBody" class="divide-y divide-slate-100">
                @forelse ($buku as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-16 overflow-hidden rounded border border-slate-200 bg-slate-100 flex items-center justify-center text-[10px] text-slate-400 font-bold uppercase">
                                    @if($item->sampul)
                                        <img src="{{ route('admin.buku.sampul', $item) }}" alt="{{ $item->judul }}" width="48" height="64" class="w-full h-full object-cover" onerror="this.remove()">
                                    @else
                                        BUKU
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $item->judul }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $item->isbn }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-cyan-50 text-cyan-700 text-[10px] font-bold rounded uppercase">{{ $item->kategori->nama ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-700">{{ $item->penulis->nama ?? '-' }}</p>
                            <p class="text-[9px] text-slate-400 uppercase mt-0.5">{{ $item->penerbit->nama ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-block text-right">
                                <p class="text-sm font-bold {{ $item->stok_tersedia == 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $item->stok_tersedia }} / {{ $item->stok }}</p>
                                <p class="text-[9px] text-slate-400 uppercase mt-0.5">{{ $item->stok_tersedia == 0 ? 'Habis' : 'Eksemplar Siap' }}</p>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center">
                            <p class="text-sm text-slate-500">Tidak ada koleksi buku yang tersedia.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="dashboardPagination" class="px-6 py-4 border-t border-slate-100">
        {{ $buku->links() }}
    </div>
</div>
@endsection

<script>
  function dashboardSearchUrl(search) {
    const url = new URL(window.location.href);
    const filters = {
      search,
      kategori_id: document.getElementById('kategoriFilter').value,
      status_ketersediaan: document.getElementById('statusFilter').value,
    };
    url.search = '';
    Object.entries(filters).forEach(([key, value]) => {
      if (value) url.searchParams.set(key, value);
    });
    return url.toString();
  }

  let dashboardSearchDebounce;
  ['kategoriFilter', 'statusFilter'].forEach((id) => {
    document.getElementById(id).addEventListener('change', () => fetchDashboardResults(document.getElementById('dashboardSearch').value));
  });
  document.getElementById('dashboardSearch').addEventListener('input', function () {
    clearTimeout(dashboardSearchDebounce);
    const value = this.value;
    dashboardSearchDebounce = setTimeout(() => fetchDashboardResults(value), 300);
  });

  function resetDashboardSearch() {
    const input = document.getElementById('dashboardSearch');
    input.value = '';
    ['kategoriFilter', 'statusFilter'].forEach((id) => document.getElementById(id).value = '');
    fetchDashboardResults('');
  }

  async function fetchDashboardResults(search) {
    try {
      const res = await fetch(dashboardSearchUrl(search));
      if (!res.ok) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const tbody = doc.getElementById('dashboardTableBody');
      const pagination = doc.getElementById('dashboardPagination');
      if (tbody) document.getElementById('dashboardTableBody').innerHTML = tbody.innerHTML;
      if (pagination) document.getElementById('dashboardPagination').innerHTML = pagination.innerHTML;
      window.history.replaceState(null, '', dashboardSearchUrl(search));
    } catch (_) {
      // abaikan: biarkan hasil terakhir tetap tampil
    }
  }
</script>
