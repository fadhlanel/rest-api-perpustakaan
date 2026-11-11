@extends('admin.layouts.app')

@section('title', 'Sirkulasi Peminjaman')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Sirkulasi Peminjaman Buku</h2>
    <p class="text-slate-500 text-sm">Monitor dan kelola pengembalian buku dari siswa.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Informasi Peminjam</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Buku yang Dipinjam</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Pinjam</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jatuh Tempo</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($peminjaman as $item)
            <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-4">
                    <p class="text-sm font-bold text-slate-800">{{ $item->nama_peminjam }}</p>
                    <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-tighter">NIS: {{ $item->nis }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm font-medium text-slate-700 leading-tight">{{ $item->buku->judul }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-xs text-slate-600">{{ $item->tanggal_pinjam->format('d M Y') }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="text-xs {{ $item->status == 'dipinjam' && $item->tanggal_jatuh_tempo->isPast() ? 'text-red-600 font-bold' : 'text-slate-600' }}">
                        {{ $item->tanggal_jatuh_tempo->format('d M Y') }}
                    </p>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 {{ $item->status == 'dipinjam' ? 'bg-orange-50 text-orange-700' : 'bg-green-50 text-green-700' }} text-[9px] font-bold rounded uppercase">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    @if($item->status == 'dipinjam')
                        <form action="{{ route('admin.peminjaman.return', $item) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-50 text-blue-700 px-3 py-1 rounded text-[10px] font-bold uppercase hover:bg-blue-100 transition">
                                Kembalikan
                            </button>
                        </form>
                    @else
                        <span class="text-[10px] text-slate-300 font-bold uppercase italic">Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="p-6 border-t border-slate-100">
        {{ $peminjaman->links() }}
    </div>
</div>
@endsection
