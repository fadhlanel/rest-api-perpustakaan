@extends('admin.layouts.app')

@section('title', 'Tambah ' . ucfirst($type))

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.master.index', $type) }}" class="hover:text-cyan-700 capitalize">{{ $type }}</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">Tambah Baru</span>
    </div>
    <h2 class="text-2xl font-bold text-slate-800 capitalize">Tambah Data {{ $type }}</h2>
    <p class="text-slate-500 text-sm">Tambahkan {{ $type }} baru yang akan tersedia di pilihan form buku.</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.master.store', $type) }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 capitalize mb-2">Nama {{ $type }}</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="{{ $type === 'kategori' ? 'Contoh: Sains Terapan' : ($type === 'penulis' ? 'Contoh: Tere Liye' : 'Contoh: Gramedia Pustaka Utama') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                @error('nama')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-cyan-700 hover:bg-cyan-800 text-white font-semibold py-3 px-6 rounded-lg text-sm shadow-sm transition">
                    Simpan {{ ucfirst($type) }}
                </button>
                <a href="{{ route('admin.master.index', $type) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-6 rounded-lg text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
