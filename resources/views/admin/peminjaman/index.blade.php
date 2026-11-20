@extends('admin.layouts.app')

@section('title', 'Sirkulasi Peminjaman')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Sirkulasi Peminjaman & Pengembalian Buku</h2>
    <p class="text-slate-500 text-sm">Monitor transaksi peminjaman, notifikasi batas waktu, dan verifikasi pengembalian mandiri siswa.</p>
</div>

<!-- Status & Notification Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <!-- 0. Bukti Pengembalian Menunggu Konfirmasi (Prioritas Tertinggi) -->
    <a href="{{ route('admin.peminjaman.index', ['status' => 'bukti_pending']) }}" class="block p-4 rounded-xl border {{ request('status') === 'bukti_pending' ? 'ring-2 ring-indigo-500 bg-indigo-50/90 border-indigo-300' : 'bg-white hover:bg-indigo-50/40 border-slate-200' }} shadow-sm transition">
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">Cek Bukti</span>
        </div>
        <p class="text-2xl font-bold text-indigo-700">{{ $stats['bukti_pending'] ?? 0 }}</p>
        <p class="text-xs text-slate-500 mt-1">Perlu konfirmasi admin</p>
    </a>

    <!-- 1. Hampir Tenggat -->
    <a href="{{ route('admin.peminjaman.index', ['status' => 'hampir_tenggat']) }}" class="block p-4 rounded-xl border {{ request('status') === 'hampir_tenggat' ? 'ring-2 ring-amber-500 bg-amber-50/80 border-amber-300' : 'bg-white hover:bg-amber-50/40 border-slate-200' }} shadow-sm transition">
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Hampir Tenggat</span>
        </div>
        <p class="text-2xl font-bold text-amber-700">{{ $stats['hampir_tenggat'] ?? 0 }}</p>
        <p class="text-xs text-slate-500 mt-1">Sisa waktu &le; 24 jam / 5 jam</p>
    </a>

    <!-- 2. Ada Siswa Mengembalikan (Kembali Mandiri + Foto) -->
    <a href="{{ route('admin.peminjaman.index', ['status' => 'kembali_mandiri']) }}" class="block p-4 rounded-xl border {{ request('status') === 'kembali_mandiri' ? 'ring-2 ring-cyan-500 bg-cyan-50/80 border-cyan-300' : 'bg-white hover:bg-cyan-50/40 border-slate-200' }} shadow-sm transition">
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-100 text-cyan-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800">Kembali Mandiri</span>
        </div>
        <p class="text-2xl font-bold text-cyan-700">{{ $stats['kembali_mandiri'] ?? 0 }}</p>
        <p class="text-xs text-slate-500 mt-1">Siswa kembalikan dengan foto</p>
    </a>

    <!-- 3. Sudah Dikembalikan -->
    <a href="{{ route('admin.peminjaman.index', ['status' => 'dikembalikan']) }}" class="block p-4 rounded-xl border {{ request('status') === 'dikembalikan' ? 'ring-2 ring-emerald-500 bg-emerald-50/80 border-emerald-300' : 'bg-white hover:bg-emerald-50/40 border-slate-200' }} shadow-sm transition">
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Sudah Kembali</span>
        </div>
        <p class="text-2xl font-bold text-emerald-700">{{ $stats['sudah_dikembalikan'] ?? 0 }}</p>
        <p class="text-xs text-slate-500 mt-1">Buku telah tersimpan kembali</p>
    </a>

    <!-- 4. Terlambat -->
    <a href="{{ route('admin.peminjaman.index', ['status' => 'terlambat']) }}" class="block p-4 rounded-xl border {{ request('status') === 'terlambat' ? 'ring-2 ring-rose-500 bg-rose-50/80 border-rose-300' : 'bg-white hover:bg-rose-50/40 border-slate-200' }} shadow-sm transition">
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-100 text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800">Terlambat</span>
        </div>
        <p class="text-2xl font-bold text-rose-700">{{ $stats['terlambat'] ?? 0 }}</p>
        <p class="text-xs text-slate-500 mt-1">Melebihi tanggal jatuh tempo</p>
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div class="relative w-full max-w-md">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="search" id="peminjamanSearch" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam, NIS, atau judul buku..." class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition">
        </div>
        <div class="flex gap-2 items-center flex-wrap">
            <select id="statusFilter" name="status" class="px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-700 hover:border-slate-300 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition cursor-pointer">
                <option value="">Semua Status</option>
                <option value="bukti_pending" @selected(request('status') === 'bukti_pending')>🟣 Cek Bukti (Prioritas Cek)</option>
                <option value="booking" @selected(request('status') === 'booking')>🟢 Booking (Menunggu Diambil)</option>
                <option value="hampir_tenggat" @selected(request('status') === 'hampir_tenggat')>🟠 Hampir Tenggat (&le; 5-24 Jam)</option>
                <option value="kembali_mandiri" @selected(request('status') === 'kembali_mandiri')>🔵 Kembali Mandiri (Dikonfirmasi)</option>
                <option value="dipinjam" @selected(request('status') === 'dipinjam')>⚪ Sedang Dipinjam</option>
                <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>🟢 Sudah Dikembalikan</option>
                <option value="terlambat" @selected(request('status') === 'terlambat')>🔴 Terlambat</option>
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
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider">Waktu & Durasi Pinjam</th>
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider">Jatuh Tempo</th>
                    <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-600 uppercase tracking-wider">Status & Notifikasi</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-600 uppercase tracking-wider">Aksi & Bukti</th>
                </tr>
            </thead>
            <tbody id="peminjamanTableBody" class="divide-y divide-slate-100 bg-white">
                @forelse($peminjaman as $item)
                    @php
                        $isLate = $item->status === 'dipinjam' && $item->tanggal_jatuh_tempo && $item->tanggal_jatuh_tempo->isPast();
                        $isBuktiPending = $item->status === 'dipinjam' && $item->pengembalian && !empty($item->pengembalian->foto_bukti) && !$item->pengembalian->dikonfirmasi;
                        $isKembaliMandiri = $item->status === 'dikembalikan' && $item->pengembalian && (!empty($item->pengembalian->foto_bukti) || $item->pengembalian->tipe_kembali === 'mandiri');
                        $isHampirTenggat = $item->status === 'dipinjam' && !$isLate && !$isBuktiPending && $item->is_hampir_tenggat;

                        if ($isBuktiPending) {
                            $badgeClass = 'bg-indigo-100 text-indigo-800 border-indigo-200 ring-2 ring-indigo-400 animate-pulse';
                            $badgeLabel = 'Perlu Cek Foto Bukti';
                            $badgeDot = 'bg-indigo-600';
                        } elseif ($isKembaliMandiri) {
                            $badgeClass = 'bg-cyan-100 text-cyan-800 border-cyan-200';
                            $badgeLabel = 'Kembali Mandiri (Foto)';
                            $badgeDot = 'bg-cyan-500';
                        } elseif ($item->is_booking) {
                            $badgeClass = 'bg-teal-50 text-teal-800 border-teal-200';
                            $badgeLabel = 'Booking (Menunggu Serah)';
                            $badgeDot = 'bg-teal-500';
                        } elseif ($item->status === 'dikembalikan') {
                            $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                            $badgeLabel = 'Sudah Dikembalikan';
                            $badgeDot = 'bg-emerald-500';
                        } elseif ($isLate) {
                            $badgeClass = 'bg-rose-100 text-rose-800 border-rose-200';
                            $badgeLabel = 'Terlambat';
                            $badgeDot = 'bg-rose-500';
                        } elseif ($isHampirTenggat) {
                            $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200 ring-1 ring-amber-400';
                            $badgeLabel = 'Hampir Tenggat';
                            $badgeDot = 'bg-amber-500 animate-ping';
                        } else {
                            $badgeClass = 'bg-blue-50 text-blue-800 border-blue-200';
                            $badgeLabel = 'Sedang Dipinjam';
                            $badgeDot = 'bg-blue-500';
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $isBuktiPending ? 'bg-indigo-50/40 border-l-4 border-indigo-500' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->nama_peminjam }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">NIS: {{ $item->nis }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-slate-800">{{ $item->buku->judul }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $item->buku->kategori?->nama ?? 'Umum' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-slate-700">{{ $item->tanggal_pinjam?->format('d M Y') }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Jam {{ $item->created_at ? $item->created_at->format('H:i') : '-' }} &bull; 
                                <span class="font-semibold text-cyan-700">{{ $item->durasi_pinjam ?? 3 }} Hari</span>
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm {{ $isLate ? 'text-rose-700 font-semibold' : ($isHampirTenggat ? 'text-amber-700 font-semibold' : 'text-slate-700') }}">
                                {{ $item->tanggal_jatuh_tempo?->format('d M Y') }}
                            </p>
                            @if($item->status === 'dipinjam')
                                <p class="text-xs {{ $isLate ? 'text-rose-600' : ($isHampirTenggat ? 'text-amber-600' : 'text-slate-400') }} mt-0.5">
                                    @if($isLate)
                                        Lewat batas pengembalian
                                    @elseif($isHampirTenggat)
                                        Tersisa &le; {{ max(1, $item->sisa_jam) }} jam lagi
                                    @else
                                        Sisa {{ $item->sisa_hari ?? 0 }} hari
                                    @endif
                                </p>
                            @elseif($item->pengembalian)
                                <p class="text-xs text-emerald-600 mt-0.5">Kembali: {{ $item->pengembalian->tanggal_kembali?->format('d M Y') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badgeDot }}"></span>
                                {{ $badgeLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                @if($isBuktiPending && $item->pengembalian?->foto_bukti)
                                    <button type="button" onclick="previewBukti('{{ route('admin.pengembalian.bukti', $item->pengembalian->id) }}', '{{ addslashes($item->nama_peminjam) }}', '{{ addslashes($item->buku->judul) }}', '{{ $item->pengembalian->tanggal_kembali?->format('d M Y') }}', {{ $item->id }})" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Periksa & Konfirmasi
                                    </button>
                                @elseif($isKembaliMandiri && $item->pengembalian?->foto_bukti)
                                    <button type="button" onclick="previewBukti('{{ route('admin.pengembalian.bukti', $item->pengembalian->id) }}', '{{ addslashes($item->nama_peminjam) }}', '{{ addslashes($item->buku->judul) }}', '{{ $item->pengembalian->tanggal_kembali?->format('d M Y') }}')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-cyan-50 hover:bg-cyan-100 text-cyan-800 border border-cyan-200 text-xs font-semibold rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Foto Bukti
                                    </button>
                                @endif

                                @if($item->is_booking)
                                    <form method="POST" action="{{ route('admin.peminjaman.serahkan-buku', $item->id) }}" onsubmit="return confirm('Serahkan buku fisik kepada {{ addslashes($item->nama_peminjam) }}? Stok akan berkurang 1 dan masa pinjam mulai berjalan.');" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Serahkan Buku
                                        </button>
                                    </form>
                                @elseif($item->status === 'dipinjam' && !$isBuktiPending)
                                    <button onclick="returnBook(this)" data-id="{{ $item->id }}" data-nama="{{ $item->nama_peminjam }}" data-nis="{{ $item->nis }}" data-judul="{{ $item->buku->judul }}" data-pinjam="{{ $item->tanggal_pinjam?->format('d M Y') }}" data-tempo="{{ $item->tanggal_jatuh_tempo?->format('d M Y') }}" class="inline-flex items-center px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                        Kembalikan
                                    </button>
                                @elseif(!$isKembaliMandiri && !$isBuktiPending)
                                    <span class="text-sm text-slate-400 font-medium">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">
                            Tidak ada data peminjaman yang cocok dengan filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div id="peminjamanPagination" class="px-6 py-4 border-t border-slate-100 bg-slate-50">
        {{ $peminjaman->links() }}
    </div>
</div>

<!-- Modal Konfirmasi Pengembalian oleh Petugas -->
<div id="returnModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50" onclick="closeReturnModal()"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6" role="dialog" aria-modal="true">
        <h3 class="text-lg font-bold text-slate-800">Konfirmasi Pengembalian Buku</h3>
        <div id="returnInfo" class="mt-3 text-sm text-slate-600 space-y-1"></div>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="closeReturnModal()" class="px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition">Batal</button>
            <button type="button" id="confirmReturnBtn" onclick="confirmReturn(this)" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm text-white font-semibold focus:outline-none transition">Ya, Kembalikan</button>
        </div>
    </div>
</div>

<!-- Modal Preview Foto Bukti Pengembalian Mandiri Siswa -->
<div id="buktiModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60" onclick="closeBuktiModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 overflow-hidden" role="dialog" aria-modal="true">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">Bukti Pengembalian di Perpustakaan</h3>
                <p id="buktiPeminjam" class="text-xs text-slate-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeBuktiModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>
        <div class="mt-4">
            <div class="bg-slate-900 rounded-xl overflow-hidden flex items-center justify-center max-h-80 border border-slate-800">
                <img id="buktiImg" src="" alt="Foto Bukti" class="max-h-80 object-contain w-full">
            </div>
            <div class="mt-3 p-3 bg-cyan-50 rounded-lg border border-cyan-100">
                <p class="text-xs font-semibold text-cyan-900">Validasi Pengembalian Mandiri:</p>
                <p class="text-xs text-cyan-700 mt-0.5">Buku telah difoto oleh siswa di meja perpustakaan saat pengembalian lebih awal tanpa menunggu persetujuan admin.</p>
                <p id="buktiTanggal" class="text-[11px] text-cyan-800 font-bold mt-1"></p>
            </div>
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" onclick="closeBuktiModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-semibold transition">Tutup</button>
            <form id="konfirmasiForm" method="POST" action="" class="hidden">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">Konfirmasi & Ubah Status Kembali</button>
            </form>
        </div>
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

  function returnBook(btn) {
    document.getElementById('returnInfo').innerHTML = `
      <p class="text-sm text-slate-700">Kembalikan buku <span class="font-semibold text-slate-900">${btn.dataset.judul}</span> yang dipinjam oleh <span class="font-semibold text-slate-900">${btn.dataset.nama}</span> (${btn.dataset.nis})?</p>
      <p class="text-xs text-slate-500 mt-2">Dipinjam: ${btn.dataset.pinjam} &middot; Jatuh tempo: ${btn.dataset.tempo}</p>
    `;
    document.getElementById('confirmReturnBtn').dataset.id = btn.dataset.id;
    document.getElementById('returnModal').classList.remove('hidden');
  }

  function closeReturnModal() {
    document.getElementById('returnModal').classList.add('hidden');
  }

  async function confirmReturn(btn) {
    const id = btn.dataset.id;
    btn.disabled = true;
    try {
      const res = await fetch(`/admin/peminjaman/${id}/return`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
      });
      if (!res.ok) throw new Error('Gagal mengembalikan buku');
      closeReturnModal();
      const search = document.getElementById('peminjamanSearch').value;
      await fetchPeminjamanResults(search);
    } catch (err) {
      alert(err.message);
    } finally {
      btn.disabled = false;
    }
  }

  function previewBukti(url, nama, judul, tanggal, peminjamanId = null) {
    document.getElementById('buktiImg').src = url;
    document.getElementById('buktiPeminjam').textContent = `${nama} — "${judul}"`;
    document.getElementById('buktiTanggal').textContent = `Tanggal Pengembalian: ${tanggal}`;
    
    const konfirmasiForm = document.getElementById('konfirmasiForm');
    if (peminjamanId) {
      konfirmasiForm.action = `/admin/peminjaman/${peminjamanId}/konfirmasi-kembali`;
      konfirmasiForm.classList.remove('hidden');
    } else {
      konfirmasiForm.classList.add('hidden');
    }
    
    document.getElementById('buktiModal').classList.remove('hidden');
  }

  function closeBuktiModal() {
    document.getElementById('buktiModal').classList.add('hidden');
    document.getElementById('buktiImg').src = '';
  }
</script>
@endsection
