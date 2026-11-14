@extends('admin.layouts.app')

@section('title', 'Sirkulasi Peminjaman')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Sirkulasi Peminjaman Buku</h2>
    <p class="text-slate-500 text-sm">Monitor dan kelola pengembalian buku dari siswa.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap gap-4 items-end">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" id="peminjamanSearch" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau judul buku..." class="flex-1 bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
        </div>
        <select id="statusFilter" name="status" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
            <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
        </select>
        <button type="button" onclick="resetPeminjamanSearch()" class="px-4 py-2 text-sm text-slate-500 flex items-center hover:text-slate-700 hover:bg-slate-100 rounded-lg whitespace-nowrap">Reset</button>
    </div>
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Informasi Peminjam</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Buku yang Dipinjam</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Pinjam</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jatuh Tempo</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="peminjamanTableBody" class="divide-y divide-slate-50">
            @foreach($peminjaman as $item)
            <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-4">
                    <p class="text-sm font-bold text-slate-800">{{ $item->nama_peminjam }}</p>
                    <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-tighter">NIS: {{ $item->nis }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm font-medium text-slate-700 leading-tight">{{ $item->buku->judul }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-xs text-slate-600">{{ $item->tanggal_pinjam->format('d M Y') }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-xs {{ $item->status == 'dipinjam' && $item->tanggal_jatuh_tempo->isPast() ? 'text-red-600 font-bold' : 'text-slate-600' }}">
                        {{ $item->tanggal_jatuh_tempo->format('d M Y') }}
                    </p>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 {{ $item->status == 'dipinjam' ? 'bg-orange-50 text-orange-700' : 'bg-green-50 text-green-700' }} text-[9px] font-bold rounded uppercase">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    @if($item->status == 'dipinjam')
                        <button onclick="returnBook({{ $item->id }})" class="bg-blue-50 text-blue-700 px-3 py-1 rounded text-[10px] font-bold uppercase hover:bg-blue-100 transition">
                            Kembalikan
                        </button>
                    @else
                        <span class="text-[10px] text-slate-300 font-bold uppercase italic">Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div id="peminjamanPagination" class="p-6 border-t border-slate-100">
        {{ $peminjaman->links() }}
    </div>
</div>

<script>
  function peminjamanSearchUrl(search) {
    const url = new URL(window.location.href);
    const filters = {
      search,
      status: document.getElementById('statusFilter').value,
    };
    url.search = '';
    Object.entries(filters).forEach(([key, value]) => {
      if (value) url.searchParams.set(key, value);
    });
    return url.toString();
  }

  let peminjamanSearchDebounce;
  document.getElementById('statusFilter').addEventListener('change', () => fetchPeminjamanResults(document.getElementById('peminjamanSearch').value));
  document.getElementById('peminjamanSearch').addEventListener('input', function () {
    clearTimeout(peminjamanSearchDebounce);
    const value = this.value;
    peminjamanSearchDebounce = setTimeout(() => fetchPeminjamanResults(value), 300);
  });

  function resetPeminjamanSearch() {
    const input = document.getElementById('peminjamanSearch');
    input.value = '';
    document.getElementById('statusFilter').value = '';
    fetchPeminjamanResults('');
  }

  async function fetchPeminjamanResults(search) {
    try {
      const res = await fetch(peminjamanSearchUrl(search));
      if (!res.ok) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const tbody = doc.getElementById('peminjamanTableBody');
      const pagination = doc.getElementById('peminjamanPagination');
      if (tbody) document.getElementById('peminjamanTableBody').innerHTML = tbody.innerHTML;
      if (pagination) document.getElementById('peminjamanPagination').innerHTML = pagination.innerHTML;
      window.history.replaceState(null, '', peminjamanSearchUrl(search));
    } catch (_) {
      // abaikan
    }
  }

  async function returnBook(id) {
    if (!confirm('Tandai buku sebagai dikembalikan?')) return;
    try {
      const res = await fetch(`/admin/peminjaman/${id}/return`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
      });
      if (!res.ok) throw new Error('Gagal mengembalikan');
      // refresh hasil search tetap di posisi yg sama
      const search = document.getElementById('peminjamanSearch').value;
      await fetchPeminjamanResults(search);
    } catch (err) {
      alert(err.message);
    }
  }
</script>
