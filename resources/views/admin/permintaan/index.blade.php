@extends('admin.layouts.app')

@section('title', 'Persetujuan Profil')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Persetujuan Perubahan Profil</h2>
    <p class="mt-1 text-sm text-slate-500">Periksa data yang diajukan siswa sebelum diterapkan.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap gap-4 items-end">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" id="permintaanSearch" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS siswa..." class="flex-1 bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
        </div>
        <select id="permintaanStatusFilter" name="status" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
        </select>
        <button type="button" onclick="resetPermintaanSearch()" class="px-4 py-2 text-sm text-slate-500 flex items-center hover:text-slate-700 hover:bg-slate-100 rounded-lg whitespace-nowrap">Reset</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left">
            <thead class="border-b border-slate-100 bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Siswa</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Perubahan</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody id="permintaanTableBody" class="divide-y divide-slate-100">
                @forelse($permintaan as $item)
                <tr>
                    <td class="px-6 py-4 align-top"><p class="font-semibold text-slate-800">{{ $item->anggota->nama }}</p><p class="mt-1 text-xs text-slate-500">NIS {{ $item->anggota->nis }}</p><p class="mt-1 text-xs text-slate-400">{{ $item->created_at->format('d M Y H:i') }}</p></td>
                    <td class="px-6 py-4 align-top"><dl class="space-y-1 text-sm">@foreach($item->perubahan as $field => $value)<div class="flex gap-2"><dt class="w-16 font-semibold text-slate-500">{{ ucfirst($field) }}</dt><dd class="text-slate-800">{{ $value ?: '(kosong)' }}</dd></div>@endforeach</dl></td>
                    <td class="px-6 py-4 align-top"><span class="rounded px-2 py-1 text-xs font-semibold {{ ['pending' => 'bg-amber-50 text-amber-700', 'disetujui' => 'bg-emerald-50 text-emerald-700', 'ditolak' => 'bg-red-50 text-red-700'][$item->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($item->status) }}</span></td>
                    <td class="px-6 py-4 text-right align-top">
                        @if($item->status === 'pending')
                        <div class="flex justify-end gap-2">
                            <form method="POST" action="{{ route('admin.permintaan.approve', $item) }}" data-confirm="Setujui permintaan ini?">
                                @csrf
                                <button class="rounded bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('admin.permintaan.reject', $item) }}" data-confirm="Tolak permintaan ini?">
                                @csrf
                                <button class="rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Tolak</button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-slate-400">{{ $item->reviewer?->name ?? 'Admin' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada permintaan perubahan profil.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="permintaanPagination" class="border-t border-slate-100 p-6">
        {{ $permintaan->links() }}
    </div>
</div>

<script>
  function permintaanSearchUrl(search) {
    const url = new URL(window.location.href);
    const filters = {
      search,
      status: document.getElementById('permintaanStatusFilter').value,
    };
    url.search = '';
    Object.entries(filters).forEach(([key, value]) => {
      if (value) url.searchParams.set(key, value);
    });
    return url.toString();
  }

  let permintaanSearchDebounce;
  document.getElementById('permintaanStatusFilter').addEventListener('change', () => fetchPermintaanResults(document.getElementById('permintaanSearch').value));
  document.getElementById('permintaanSearch').addEventListener('input', function () {
    clearTimeout(permintaanSearchDebounce);
    const value = this.value;
    permintaanSearchDebounce = setTimeout(() => fetchPermintaanResults(value), 300);
  });

  function resetPermintaanSearch() {
    const input = document.getElementById('permintaanSearch');
    input.value = '';
    document.getElementById('permintaanStatusFilter').value = '';
    fetchPermintaanResults('');
  }

  async function fetchPermintaanResults(search) {
    try {
      const res = await fetch(permintaanSearchUrl(search));
      if (!res.ok) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const tbody = doc.getElementById('permintaanTableBody');
      const pagination = doc.getElementById('permintaanPagination');
      if (tbody) document.getElementById('permintaanTableBody').innerHTML = tbody.innerHTML;
      if (pagination) document.getElementById('permintaanPagination').innerHTML = pagination.innerHTML;
      window.history.replaceState(null, '', permintaanSearchUrl(search));
    } catch (_) {
      // abaikan
    }
  }
</script>
@endsection
