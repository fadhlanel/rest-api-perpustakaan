@extends('admin.layouts.app')

@section('title', 'Master ' . ucfirst($type))

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.buku.index') }}" class="hover:text-cyan-700">Katalog Buku</a>
            <span>/</span>
            <span class="text-slate-800 font-medium capitalize">{{ $type }}</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 capitalize">Data Master {{ $type }}</h2>
        <p class="text-slate-500 text-sm">Kelola referensi {{ $type }} untuk metadata koleksi buku perpustakaan.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.buku.create') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Form Buku
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Baru -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sticky top-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-1">Tambah {{ ucfirst($type) }} Baru</h3>
            <p class="text-xs text-slate-500 mb-4">Data langsung tersedia di pilihan form buku.</p>

            <form method="POST" action="{{ route('admin.master.store', $type) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Nama {{ ucfirst($type) }}</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: {{ $type === 'kategori' ? 'Sains Terapan' : ($type === 'penulis' ? 'Tere Liye' : 'Gramedia Pustaka') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                    @error('nama')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-cyan-700 hover:bg-cyan-800 text-white font-semibold py-2.5 px-4 rounded-lg text-sm shadow-sm transition">
                    Simpan {{ ucfirst($type) }}
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Daftar {{ ucfirst($type) }} Terdaftar</h3>
                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">{{ $items->total() }} entri</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">#</th>
                            <th class="py-3 px-6">Nama {{ ucfirst($type) }}</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $index => $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-6 text-xs text-slate-400 font-mono">{{ $items->firstItem() + $index }}</td>
                                <td class="py-3.5 px-6 font-medium text-slate-800">{{ $item->nama }}</td>
                                <td class="py-3.5 px-6 text-right">
                                    <form method="POST" action="{{ route('admin.master.destroy', [$type, $item->id]) }}" onsubmit="return confirm('Hapus {{ $type }} ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center text-slate-400">
                                    <p class="text-sm">Belum ada data {{ $type }}.</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan formulir di sebelah kiri untuk menambahkan data pertama.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
