@extends('admin.layouts.app')

@section('title', 'Tambah Siswa / Anggota')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.anggota.index') }}" class="hover:text-cyan-700">Data Siswa / Anggota</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">Tambah</span>
    </div>
    <h2 class="text-2xl font-bold text-slate-800">Tambah Siswa / Anggota</h2>
    <p class="text-slate-500 text-sm">Siswa dapat langsung login di aplikasi Android menggunakan NIS dan PIN ini.</p>
</div>

<div class="max-w-xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <form method="POST" action="{{ route('admin.anggota.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">NIS (Nomor Induk Siswa) <span class="text-red-500">*</span></label>
            <input type="text" name="nis" value="{{ old('nis') }}" required placeholder="Contoh: 20241001" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            @error('nis')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Ahmad Fauzan" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            @error('nama')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kelas / Jurusan</label>
            <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: XII RPL 1" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            @error('kelas')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">PIN Login (min. 4 digit) <span class="text-red-500">*</span></label>
            <input type="password" name="pin" required placeholder="Contoh: 123456" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            <p class="text-[11px] text-slate-400 mt-0.5">PIN digunakan siswa untuk masuk di aplikasi Android.</p>
            @error('pin')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Email (Opsional)</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="siswa@sekolah.sch.id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            @error('email')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Telepon (Opsional)</label>
            <input type="text" name="telepon" value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            @error('telepon')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-cyan-700 hover:bg-cyan-800 text-white font-semibold py-2.5 px-5 rounded-lg text-sm shadow-sm transition">
                Simpan Data Siswa
            </button>
            <a href="{{ route('admin.anggota.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Batal</a>
        </div>
    </form>
</div>
@endsection
