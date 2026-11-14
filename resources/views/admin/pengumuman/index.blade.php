@extends('admin.layouts.app')

@section('title', 'Pengumuman Perpustakaan')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Pengumuman Perpustakaan</h2>
    <p class="text-slate-500 text-sm">Informasi yang dibuat di sini langsung muncul pada banner pengumuman di aplikasi Android siswa.</p>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <!-- Tombol Tambah Pengumuman -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <button id="tambahPengumumanBtn" class="inline-flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-sm transition" aria-label="Tambah pengumuman baru">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Tambah Pengumuman
            </button>
        </div>
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" id="pengumumanSearch" name="search" value="{{ request('search') }}" placeholder="Cari judul atau isi pengumuman..." class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
        </div>
        <select id="pengumumanStatusFilter" name="aktif" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="1" @selected(request('aktif') === '1')>Aktif</option>
            <option value="0" @selected(request('aktif') === '0')>Nonaktif</option>
        </select>
        <button type="button" onclick="resetPengumumanSearch()" class="px-4 py-2 text-sm text-slate-500 flex items-center hover:text-slate-700 hover:bg-slate-100 rounded-lg whitespace-nowrap">Reset</button>
    </div>

    <!-- Dialog Form Tambah/Edit Pengumuman -->
    <dialog id="pengumumanDialog" class="fixed inset-0 m-auto max-w-[min(92vw,32rem)] w-full rounded-xl p-0 shadow-xl backdrop:bg-slate-900/40" aria-label="Form pengumuman">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tulis Pengumuman</h3>
                    <p class="text-xs text-slate-500 mt-1">Pengumuman aktif akan disinkronkan ke aplikasi Android.</p>
                </div>
                <button type="button" onclick="document.getElementById('pengumumanDialog').close()" class="text-slate-400 hover:text-slate-600 rounded p-1" aria-label="Tutup dialog">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pengumuman.store') }}" id="pengumumanForm" class="space-y-4">
                @csrf

                <div>
                    <label for="pengumumanJudul" class="block text-xs font-bold text-slate-600 uppercase mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                    <input type="text" id="pengumumanJudul" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Jadwal Pengembalian Buku Ujian" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                    @error('judul')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pengumumanIsi" class="block text-xs font-bold text-slate-600 uppercase mb-1">Isi Pengumuman <span class="text-red-500">*</span></label>
                    <textarea id="pengumumanIsi" name="isi" rows="5" required placeholder="Tuliskan isi pengumuman secara rinci..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">{{ old('isi') }}</textarea>
                    @error('isi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="aktif" value="1" {{ old('aktif', '1') ? 'checked' : '' }} class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500">
                        <span class="text-xs font-medium text-slate-700">Tampilkan langsung (status aktif)</span>
                    </label>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="flex-1 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold py-2.5 px-4 rounded-lg text-sm shadow-sm transition">
                        Terbitkan Pengumuman
                    </button>
                    <button type="button" onclick="document.getElementById('pengumumanDialog').close()" class="px-4 py-2.5 text-sm text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 whitespace-nowrap">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Daftar Pengumuman -->
    <div class="p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Daftar Pengumuman Terbit</h3>
            <span class="text-xs font-medium text-slate-500 bg-white border border-slate-200 px-2.5 py-1 rounded-full shadow-2xs">{{ $pengumuman->total() }} entri</span>
        </div>

        <div class="space-y-3" id="pengumumanList">
            @forelse($pengumuman as $item)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:border-slate-300 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                @if($item->aktif)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif di Android
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                                        Draft / Nonaktif
                                    </span>
                                @endif
                                <span class="text-xs text-slate-400 font-mono">{{ $item->created_at?->diffForHumans() ?? 'Baru saja' }}</span>
                            </div>
                            <h4 class="text-base font-bold text-slate-800">{{ $item->judul }}</h4>
                            <p class="mt-2 text-sm text-slate-600 whitespace-pre-line leading-relaxed">{{ $item->isi }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.pengumuman.destroy', $item) }}" data-confirm="Hapus pengumuman ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline p-1">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-300 p-12 text-center text-slate-400">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <p class="text-sm font-medium text-slate-600">Belum ada pengumuman.</p>
                    <p class="text-xs text-slate-400 mt-1">Tulis pengumuman di atas untuk menyampaikan informasi ke seluruh aplikasi siswa.</p>
                </div>
            @endforelse
        </div>

        @if($pengumuman->hasPages())
            <div class="pt-4" id="pengumumanPagination">
                {{ $pengumuman->links() }}
            </div>
        @endif
    </div>
</div>

<script>
  function pengumumanSearchUrl(search) {
    const url = new URL(window.location.href);
    const filters = {
      search,
      aktif: document.getElementById('pengumumanStatusFilter').value,
    };
    url.search = '';
    Object.entries(filters).forEach(([key, value]) => {
      if (value) url.searchParams.set(key, value);
    });
    return url.toString();
  }

  let pengumumanSearchDebounce;
  document.getElementById('pengumumanStatusFilter').addEventListener('change', () => fetchPengumumanResults(document.getElementById('pengumumanSearch').value));
  document.getElementById('pengumumanSearch').addEventListener('input', function () {
    clearTimeout(pengumumanSearchDebounce);
    const value = this.value;
    pengumumanSearchDebounce = setTimeout(() => fetchPengumumanResults(value), 300);
  });

  function resetPengumumanSearch() {
    const input = document.getElementById('pengumumanSearch');
    input.value = '';
    document.getElementById('pengumumanStatusFilter').value = '';
    fetchPengumumanResults('');
  }

  async function fetchPengumumanResults(search) {
    try {
      const res = await fetch(pengumumanSearchUrl(search));
      if (!res.ok) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const list = doc.getElementById('pengumumanList');
      const pagination = doc.getElementById('pengumumanPagination');
      if (list) document.getElementById('pengumumanList').innerHTML = list.innerHTML;
      if (pagination) document.getElementById('pengumumanPagination').innerHTML = pagination.innerHTML;
      window.history.replaceState(null, '', pengumumanSearchUrl(search));
    } catch (_) {
      // abaikan
    }
  }

  // Dialog pengumuman
  document.getElementById('tambahPengumumanBtn').addEventListener('click', () => {
    document.getElementById('pengumumanDialog').showModal();
    // Reset form
    document.getElementById('pengumumanForm').reset();
  });

  // Close dialog on Escape key
  document.getElementById('pengumumanDialog').addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.getElementById('pengumumanDialog').close();
    }
  });
</script>
@endsection