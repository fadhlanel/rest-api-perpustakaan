@extends('admin.layouts.app')

@section('title', 'Katalog & Master Buku')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Master Data & Katalog Buku</h2>
        <p class="text-slate-500 text-sm">Kelola koleksi pustaka dan sinkronisasi aplikasi mobile.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.buku.export') }}" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
            Export CSV
        </a>
        <a href="{{ route('admin.buku.export-pdf') }}" class="flex items-center gap-1 bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Export Label / PDF
        </a>
        <a href="{{ route('admin.buku.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            + Tambah Koleksi Buku
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[200px]">
            <label for="bukuSearch" class="sr-only">Cari buku</label>
            <input type="search" name="search" id="bukuSearch" value="{{ request('search') }}" placeholder="Cari judul, ISBN, penulis..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
        </div>
        <select name="kategori_id" id="kategoriFilter" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Semua kategori</option>
            @foreach($kategori as $item)<option value="{{ $item->id }}" @selected(request('kategori_id') == $item->id)>{{ $item->nama }}</option>@endforeach
        </select>
        <select name="penulis_id" id="penulisFilter" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Semua penulis</option>
            @foreach($penulis as $item)<option value="{{ $item->id }}" @selected(request('penulis_id') == $item->id)>{{ $item->nama }}</option>@endforeach
        </select>
        <select name="penerbit_id" id="penerbitFilter" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Semua penerbit</option>
            @foreach($penerbit as $item)<option value="{{ $item->id }}" @selected(request('penerbit_id') == $item->id)>{{ $item->nama }}</option>@endforeach
        </select>
        <a href="{{ route('admin.buku.import') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-700 transition whitespace-nowrap">Import CSV</a>
        <button type="button" onclick="resetBukuSearch()" class="px-4 py-2 text-sm text-slate-500 flex items-center hover:text-slate-700 hover:bg-slate-100 rounded-lg whitespace-nowrap">Reset</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cover & Informasi Buku</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kategori</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Penulis & Penerbit</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Stok Koleksi</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="bukuTableBody" class="divide-y divide-slate-50">
                @foreach($buku as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-16 bg-slate-100 rounded overflow-hidden flex-shrink-0 border border-slate-200">
                                @if($item->sampul)
                                    <img src="{{ route('admin.buku.sampul', $item) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-[10px] text-slate-300 font-bold uppercase text-center p-1 leading-tight\'>No Cover</div>'">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-300 font-bold uppercase text-center p-1 leading-tight">No Cover</div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $item->judul }}</p>
                                <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-tighter">ISBN: {{ $item->isbn ?? '-' }} • {{ $item->tahun_terbit }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-cyan-50 text-cyan-700 text-[10px] font-bold rounded uppercase">
                            {{ $item->kategori?->nama ?? 'Tanpa Kategori' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-slate-700">{{ $item->penulis?->nama ?? 'Tanpa Penulis' }}</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $item->penerbit?->nama ?? 'Tanpa Penerbit' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-block text-right">
                            <p class="text-sm font-bold {{ $item->stok_tersedia == 0 ? 'text-red-600' : 'text-slate-800' }}">
                                {{ $item->stok_tersedia }} / {{ $item->stok }}
                            </p>
                            <p class="text-[9px] text-slate-400 uppercase mt-0.5">Eksemplar Siap</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-1.5 flex-wrap">
                            {{-- Detail --}}
                            <a href="{{ route('admin.buku.show', $item) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 text-xs font-semibold hover:bg-cyan-100 transition whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                            {{-- Edit --}}
                            <a href="{{ route('admin.buku.edit', $item) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold hover:bg-blue-100 transition whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            {{-- Hapus --}}
                            <form action="{{ route('admin.buku.destroy', $item) }}" method="POST" data-confirm="Hapus buku ini?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-100 transition whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div id="bukuPagination" class="p-6 border-t border-slate-100">
        {{ $buku->links() }}
    </div>
</div>

<script>
  function bukuSearchUrl(search) {
    const url = new URL(window.location.href);
    const filters = {
      search,
      kategori_id: document.getElementById('kategoriFilter').value,
      penulis_id: document.getElementById('penulisFilter').value,
      penerbit_id: document.getElementById('penerbitFilter').value,
    };
    url.search = '';
    Object.entries(filters).forEach(([key, value]) => {
      if (value) url.searchParams.set(key, value);
    });
    return url.toString();
  }

  let bukuSearchDebounce;
  ['kategoriFilter', 'penulisFilter', 'penerbitFilter'].forEach((id) => {
    document.getElementById(id).addEventListener('change', () => fetchBukuResults(document.getElementById('bukuSearch').value));
  });
  document.getElementById('bukuSearch').addEventListener('input', function () {
    clearTimeout(bukuSearchDebounce);
    const value = this.value;
    bukuSearchDebounce = setTimeout(() => fetchBukuResults(value), 300);
  });

  function resetBukuSearch() {
    const input = document.getElementById('bukuSearch');
    input.value = '';
    ['kategoriFilter', 'penulisFilter', 'penerbitFilter'].forEach((id) => document.getElementById(id).value = '');
    fetchBukuResults('');
  }

  async function fetchBukuResults(search) {
    try {
      const res = await fetch(bukuSearchUrl(search));
      if (!res.ok) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const tbody = doc.getElementById('bukuTableBody');
      const pagination = doc.getElementById('bukuPagination');
      if (tbody) document.getElementById('bukuTableBody').innerHTML = tbody.innerHTML;
      if (pagination) document.getElementById('bukuPagination').innerHTML = pagination.innerHTML;
      window.history.replaceState(null, '', bukuSearchUrl(search));
    } catch (_) {
      // abaikan: biarkan hasil terakhir tetap tampil
    }
  }
</script>
@endsection