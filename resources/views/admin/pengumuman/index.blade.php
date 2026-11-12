@extends('admin.layouts.app')

@section('title', 'Pengumuman Perpustakaan')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Pengumuman</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-800">Pengumuman Perpustakaan</h2>
        <p class="text-slate-500 text-sm">Informasi yang dibuat di sini langsung muncul pada banner pengumuman di aplikasi Android siswa.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Buat Pengumuman -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sticky top-6">
            <div class="border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tulis Pengumuman</h3>
                <p class="text-xs text-slate-500">Pengumuman aktif akan disinkronkan ke aplikasi Android.</p>
            </div>

            <form method="POST" action="{{ route('admin.pengumuman.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Jadwal Pengembalian Buku Ujian" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                    @error('judul')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Isi Pengumuman <span class="text-red-500">*</span></label>
                    <textarea name="isi" rows="5" required placeholder="Tuliskan isi pengumuman secara rinci..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">{{ old('isi') }}</textarea>
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

                <button type="submit" class="w-full bg-cyan-700 hover:bg-cyan-800 text-white font-semibold py-2.5 px-4 rounded-lg text-sm shadow-sm transition">
                    Terbitkan Pengumuman
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Pengumuman -->
    <div class="lg:col-span-2 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Daftar Pengumuman Terbit</h3>
            <span class="text-xs font-medium text-slate-500 bg-white border border-slate-200 px-2.5 py-1 rounded-full shadow-2xs">{{ $pengumuman->total() }} entri</span>
        </div>

        <div class="space-y-3">
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
                        <form method="POST" action="{{ route('admin.pengumuman.destroy', $item) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
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
                    <p class="text-xs text-slate-400 mt-1">Tulis pengumuman di sebelah kiri untuk menyampaikan informasi ke seluruh aplikasi siswa.</p>
                </div>
            @endforelse
        </div>

        @if($pengumuman->hasPages())
            <div class="pt-4">
                {{ $pengumuman->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
