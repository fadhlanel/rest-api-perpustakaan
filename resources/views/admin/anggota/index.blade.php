@extends('admin.layouts.app')

@section('title', 'Data Siswa & Anggota')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Data Siswa / Anggota</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-800">Data Siswa & Anggota</h2>
        <p class="text-slate-500 text-sm">Kelola akun siswa peminjam buku. Akun di sini digunakan siswa untuk login di aplikasi Android.</p>
    </div>
    <a href="{{ route('admin.anggota.create') }}" class="inline-flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold px-5 py-2.5 rounded-lg text-sm shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Siswa / Anggota
    </a>
</div>

<div class="space-y-4">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <div class="flex gap-2 items-end">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="search" id="anggotaSearch" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau kelas siswa..." class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            <button type="button" onclick="resetAnggotaSearch()" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg whitespace-nowrap">Reset</button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Daftar Siswa Terdaftar</h3>
            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">{{ $anggota->total() }} siswa</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">NIS</th>
                        <th class="py-3 px-6">Nama Lengkap</th>
                        <th class="py-3 px-6">Kelas</th>
                        <th class="py-3 px-6">Kontak</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="anggotaTableBody" class="divide-y divide-slate-100">
                    @forelse($anggota as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-mono font-semibold text-xs text-cyan-800">{{ $item->nis }}</td>
                            <td class="py-3.5 px-6 font-medium text-slate-800">{{ $item->nama }}</td>
                            <td class="py-3.5 px-6">
                                <span class="inline-block bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded">{{ $item->kelas ?: '-' }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $item->telepon ?: ($item->email ?: '-') }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <form method="POST" action="{{ route('admin.anggota.destroy', $item) }}" data-confirm="Hapus siswa {{ $item->nama }}? Jika siswa sedang login di aplikasi, sesinya akan langsung berakhir.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-medium">Belum ada data siswa.</p>
                                <p class="text-xs text-slate-400 mt-1">Klik "Tambah Siswa / Anggota" agar siswa dapat masuk ke aplikasi Android.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="anggotaPagination">
            @if($anggota->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $anggota->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
  function anggotaSearchUrl(search) {
    const url = new URL(window.location.href);
    url.search = search ? '?search=' + encodeURIComponent(search) : '';
    return url.toString();
  }

  let anggotaSearchDebounce;
  document.getElementById('anggotaSearch').addEventListener('input', function () {
    clearTimeout(anggotaSearchDebounce);
    const value = this.value;
    anggotaSearchDebounce = setTimeout(() => fetchAnggotaResults(value), 300);
  });

  function resetAnggotaSearch() {
    document.getElementById('anggotaSearch').value = '';
    fetchAnggotaResults('');
  }

  async function fetchAnggotaResults(search) {
    try {
      const res = await fetch(anggotaSearchUrl(search));
      if (!res.ok) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const tbody = doc.getElementById('anggotaTableBody');
      const pagination = doc.getElementById('anggotaPagination');
      if (tbody) document.getElementById('anggotaTableBody').innerHTML = tbody.innerHTML;
      if (pagination) document.getElementById('anggotaPagination').innerHTML = pagination.innerHTML;
      window.history.replaceState(null, '', anggotaSearchUrl(search));
    } catch (_) {
      // abaikan
    }
  }
</script>
@endsection
