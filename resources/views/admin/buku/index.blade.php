@extends('admin.layouts.app')

@section('title', 'Katalog & Master Buku')

@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Master Data & Katalog Buku</h2>
        <p class="text-slate-500 text-sm">Kelola koleksi pustaka dan sinkronisasi aplikasi mobile.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.buku.export') }}" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
            Export CSV
        </a>
        <a href="{{ route('admin.buku.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            + Tambah Koleksi Buku
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex flex-wrap gap-4 items-end">
        <div class="relative flex-1 min-w-[200px]">
            <input type="search" name="search" id="bukuSearch" value="{{ request('search') }}" placeholder="Cari judul atau ISBN (realtime)..." class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
        </div>
        <a href="{{ route('admin.buku.import') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-700 transition whitespace-nowrap">Import CSV</a>
        <button type="button" onclick="resetBukuSearch()" class="px-4 py-2 text-sm text-slate-500 flex items-center hover:text-slate-700 hover:bg-slate-100 rounded-lg whitespace-nowrap">Reset</button>
    </div>

    <table class="w-full text-left">
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
                        {{ $item->kategori->nama }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <p class="text-xs font-bold text-slate-700">{{ $item->penulis->nama }}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">{{ $item->penerbit->nama }}</p>
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
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.buku.edit', $item) }}" class="p-1.5 text-slate-400 hover:text-blue-600 transition">
                            Edit
                        </a>
                        <form action="{{ route('admin.buku.destroy', $item) }}" method="POST" data-confirm="Hapus buku ini?">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div id="bukuPagination" class="p-6 border-t border-slate-100">
        {{ $buku->links() }}
    </div>
</div>

<script>
  function bukuSearchUrl(search) {
    const url = new URL(window.location.href);
    url.searchParams.set('search', search);
    return url.toString();
  }

  let bukuSearchDebounce;
  document.getElementById('bukuSearch').addEventListener('input', function () {
    clearTimeout(bukuSearchDebounce);
    const value = this.value;
    bukuSearchDebounce = setTimeout(() => fetchBukuResults(value), 300);
  });

  function resetBukuSearch() {
    const input = document.getElementById('bukuSearch');
    input.value = '';
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
