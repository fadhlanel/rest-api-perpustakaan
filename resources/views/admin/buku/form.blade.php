@extends('admin.layouts.app')

@section('title', isset($buku) ? 'Edit Buku' : 'Tambah Buku')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">{{ isset($buku) ? 'Edit Koleksi Buku' : 'Tambah Koleksi Buku' }}</h2>
    <p class="text-slate-500 text-sm">Lengkapi metadata buku untuk katalog perpustakaan.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-8">
    <form action="{{ isset($buku) ? route('admin.buku.update', $buku) : route('admin.buku.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($buku)) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Judul Buku</label>
                    <input type="text" name="judul" value="{{ old('judul', $buku->judul ?? '') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $buku->isbn ?? '') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit ?? date('Y')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Total Stok</label>
                        <input type="number" name="stok" value="{{ old('stok', $buku->stok ?? 0) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500" required min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('deskripsi', $buku->deskripsi ?? '') }}</textarea>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Kategori</label>
                    <select name="kategori_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategori as $cat)
                            <option value="{{ $cat->id }}" {{ (old('kategori_id', $buku->kategori_id ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Penulis</label>
                    <select name="penulis_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Penulis</option>
                        @foreach($penulis as $p)
                            <option value="{{ $p->id }}" {{ (old('penulis_id', $buku->penulis_id ?? '') == $p->id) ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Penerbit</label>
                    <select name="penerbit_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Penerbit</option>
                        @foreach($penerbit as $pub)
                            <option value="{{ $pub->id }}" {{ (old('penerbit_id', $buku->penerbit_id ?? '') == $pub->id) ? 'selected' : '' }}>{{ $pub->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Sampul Buku</label>
                    @if(isset($buku) && $buku->sampul)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $buku->sampul) }}" class="w-20 h-28 object-cover rounded border border-slate-200">
                        </div>
                    @endif
                    <input type="file" name="sampul" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.buku.index') }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-lg transition">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-md transition">
                Simpan Koleksi
            </button>
        </div>
    </form>
</div>
@endsection
