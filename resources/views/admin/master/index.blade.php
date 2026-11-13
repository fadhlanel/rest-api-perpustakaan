@extends('admin.layouts.app')

@section('title', 'Master ' . ucfirst($type))

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.buku.index') }}" class="hover:text-cyan-700">Katalog Buku</a>
            <span>/</span>
            <span class="text-slate-800 font-medium capitalize">{{ $type }}</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 capitalize">Data Master {{ $type }}</h2>
        <p class="text-slate-500 text-sm">Kelola referensi {{ $type }} untuk metadata koleksi buku perpustakaan.</p>
    </div>
    <a href="{{ route('admin.master.create', $type) }}" class="inline-flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold px-5 py-2.5 rounded-lg text-sm shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah {{ ucfirst($type) }}
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" id="masterSearch" name="search" value="{{ request('search') }}" placeholder="Cari nama {{ $type }}..." class="flex-1 bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
        </div>
        <button type="button" onclick="resetMasterSearch()" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg whitespace-nowrap">Reset</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="py-3 px-6">#</th>
                    <th class="py-3 px-6 capitalize">Nama {{ $type }}</th>
                    <th class="py-3 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="masterTableBody" class="divide-y divide-slate-100">
                @forelse($items as $index => $item)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-6 text-xs text-slate-400 font-mono">{{ $items->firstItem() + $index }}</td>
                        <td class="py-3.5 px-6 font-medium text-slate-800">{{ $item->nama }}</td>
                        <td class="py-3.5 px-6 text-right">
                            <div class="flex gap-3 justify-end">
                                <form method="POST" action="{{ route('admin.master.destroy', [$type, $item->id]) }}" data-confirm="Hapus {{ $type }} ini?" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-12 text-center text-slate-400">
                            <p class="text-sm">Belum ada data {{ $type }}.</p>
                            <p class="text-xs text-slate-400 mt-1">Klik "Tambah {{ ucfirst($type) }}" untuk menambahkan data pertama.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="masterPagination" class="px-6 py-4 border-t border-slate-100">
        @if($items->hasPages())
            {{ $items->links() }}
        @endif
    </div>
</div>

<script>
  function masterSearchUrl(search) {
    const url = new URL(window.location.href);
    url.search = search ? '?search=' + encodeURIComponent(search) : '';
    return url.toString();
  }

  let masterSearchDebounce;
  document.getElementById('masterSearch').addEventListener('input', function () {
    clearTimeout(masterSearchDebounce);
    const value = this.value;
    masterSearchDebounce = setTimeout(() => fetchMasterResults(value), 300);
  });

  function resetMasterSearch() {
    document.getElementById('masterSearch').value = '';
    fetchMasterResults('');
  }

  async function fetchMasterResults(search) {
    try {
      const res = await fetch(masterSearchUrl(search));
      if (!res.ok) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const tbody = doc.getElementById('masterTableBody');
      const pagination = doc.getElementById('masterPagination');
      if (tbody) document.getElementById('masterTableBody').innerHTML = tbody.innerHTML;
      if (pagination) document.getElementById('masterPagination').innerHTML = pagination.innerHTML;
      window.history.replaceState(null, '', masterSearchUrl(search));
    } catch (_) {
      // ignore
    }
  }
</script>
@endsection
