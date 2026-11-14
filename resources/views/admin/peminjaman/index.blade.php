@extends('admin.layouts.app')

@section('title', 'Sirkulasi Peminjaman')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Sirkulasi Peminjaman Buku</h2>
    <p class="text-slate-500 text-sm">Monitor dan kelola peminjaman buku dari siswa.</p>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div class="relative w-full max-w-md">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" id="peminjamanSearch" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau judul buku..." class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition">
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <select id="statusFilter" name="status" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-700 hover:border-slate-300 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition cursor-pointer">
                <option value="">Semua Status</option>
                <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
                <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
            </select>
            <button type="button" onclick="resetPeminjamanSearch()" class="px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 focus:outline-none transition">Reset</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider">Informasi Peminjam</th>
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider">Buku yang Dipinjam</th>
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider">Tanggal Pinjam</th>
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider">Jatuh Tempo</th>
                    <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="peminjamanTableBody" class="divide-y divide-slate-100 bg-white">
                @foreach($peminjaman as $item)
                    @php
                        $isLate = $item->status == 'dipinjam' && $item->tanggal_jatuh_tempo->isPast();
                        $statusClass = $isLate
                            ? 'bg-red-100 text-red-800'
                            : ($item->status == 'dipinjam'
                                ? 'bg-amber-100 text-amber-800'
                                : 'bg-green-100 text-green-800');
                        $statusLabel = $isLate ? 'Terlambat' : ucfirst(str_replace('_', ' ', $item->status));
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->nama_peminjam }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">NIS: {{ $item->nis }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-slate-800">{{ $item->buku->judul }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-slate-700">{{ $item->tanggal_pinjam->format('d M Y') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm {{ $isLate ? 'text-red-700 font-semibold' : 'text-slate-700' }}">
                                {{ $item->tanggal_jatuh_tempo->format('d M Y') }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if($item->status == 'dipinjam')
                                <button onclick="returnBook({{ $item->id }})" class="inline-flex items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Kembalikan
                                </button>
                            @else
                                <span class="text-sm text-slate-400 font-medium">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div id="peminjamanPagination" class="px-6 py-4 border-t border-slate-100 bg-slate-50">
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
