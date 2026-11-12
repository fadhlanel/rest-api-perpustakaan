@extends('admin.layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">Pengaturan Sistem</span>
    </div>
    <h2 class="text-2xl font-bold text-slate-800">Pengaturan Sistem Perpustakaan</h2>
    <p class="text-slate-500 text-sm">Konfigurasi operasional sirkulasi, identitas aplikasi, dan status layanan.</p>
</div>

<div class="max-w-3xl">
    <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-800">Identitas Lembaga</h3>
                <p class="text-xs text-slate-500">Nama yang tampil pada kop aplikasi web dan aplikasi Android.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Nama Perpustakaan</label>
                    <input type="text" name="nama_perpustakaan" value="{{ old('nama_perpustakaan', $settings['nama_perpustakaan']) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                    @error('nama_perpustakaan')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Nama Sekolah / Unit</label>
                    <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $settings['nama_sekolah']) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                    @error('nama_sekolah')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-800">Aturan Peminjaman</h3>
                <p class="text-xs text-slate-500">Parameter yang menentukan durasi jatuh tempo transaksi sirkulasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Durasi Peminjaman (hari)</label>
                    <input type="number" name="durasi_peminjaman" min="1" max="30" value="{{ old('durasi_peminjaman', $settings['durasi_peminjaman']) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                    <p class="text-[11px] text-slate-400 mt-1">Standar default: 3 hari.</p>
                    @error('durasi_peminjaman')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Maks. Buku per Siswa</label>
                    <input type="number" name="batas_buku" min="1" max="10" value="{{ old('batas_buku', $settings['batas_buku']) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                    <p class="text-[11px] text-slate-400 mt-1">Batas pinjaman aktif bersamaan.</p>
                    @error('batas_buku')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Durasi Perpanjangan (hari)</label>
                    <input type="number" name="durasi_perpanjangan" min="1" max="14" value="{{ old('durasi_perpanjangan', $settings['durasi_perpanjangan']) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                    <p class="text-[11px] text-slate-400 mt-1">Tambahan waktu 1x perpanjangan.</p>
                    @error('durasi_perpanjangan')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-800">Status Operasional Layanan</h3>
                <p class="text-xs text-slate-500">Status publik perpustakaan yang dapat dibaca oleh client.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Status</label>
                <select name="status_layanan" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                    <option value="aktif" {{ old('status_layanan', $settings['status_layanan']) === 'aktif' ? 'selected' : '' }}>Aktif (Layanan Buka Normal)</option>
                    <option value="pemeliharaan" {{ old('status_layanan', $settings['status_layanan']) === 'pemeliharaan' ? 'selected' : '' }}>Pemeliharaan (Stock Opname / Maintenance)</option>
                    <option value="tutup" {{ old('status_layanan', $settings['status_layanan']) === 'tutup' ? 'selected' : '' }}>Tutup (Libur Semester / Nasional)</option>
                </select>
                @error('status_layanan')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition">Batal</a>
            <button type="submit" class="bg-cyan-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-cyan-800 shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
