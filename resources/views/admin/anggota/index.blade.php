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
                        <th class="py-3 px-6">PIN</th>
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
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-2">
                                    <code id="pin-{{ $item->id }}" class="max-w-28 truncate rounded bg-slate-100 px-2 py-1 text-xs text-slate-600">••••••••</code>
                                    <button type="button" onclick="togglePin('{{ route('admin.anggota.showPin', $item) }}', 'pin-{{ $item->id }}', this)" class="text-xs font-medium text-cyan-600 hover:text-cyan-800 hover:underline">Lihat</button>
                                </div>
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex justify-end items-center gap-1.5 flex-wrap">
                                    {{-- Detail --}}
                                    <a href="{{ route('admin.anggota.show', $item) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 text-xs font-semibold hover:bg-cyan-100 transition whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>

                                    {{-- Reset PIN --}}
                                    <form method="POST" action="{{ route('admin.anggota.resetPin', $item) }}"
                                          data-confirm="Reset PIN untuk {{ $item->nama }}? Siswa harus membuat PIN baru saat login di aplikasi Android.">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold hover:bg-amber-100 transition whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Reset PIN
                                        </button>
                                    </form>

                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('admin.anggota.destroy', $item) }}"
                                          data-confirm="Hapus siswa {{ $item->nama }}? Jika siswa sedang login di aplikasi, sesinya akan langsung berakhir.">
                                        @csrf
                                        @method('DELETE')
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
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
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

  async function togglePin(url, elementId, button) {
    const target = document.getElementById(elementId);
    const isVisible = button.textContent.trim() === 'Tutup';
    if (isVisible) {
      target.textContent = '••••••••';
      button.textContent = 'Lihat';
      return;
    }

    try {
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
      });
      if (!res.ok) throw new Error('Gagal mengambil data.');
      const data = await res.json();
      target.textContent = data.pin;
      button.textContent = 'Tutup';
    } catch (e) {
      alert('Gagal memuat data PIN.');
    }
  }
</script>
@endsection
