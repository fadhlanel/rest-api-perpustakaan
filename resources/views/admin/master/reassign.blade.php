@extends('admin.layouts.app')

@section('title', 'Pindah ' . ucfirst($type) . ' Buku Yatim')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('admin.master.index', $type) }}" class="hover:text-cyan-700">Master {{ ucfirst($type) }}</a>
            <span>/</span>
            <span class="text-slate-800 font-medium capitalize">Pindah {{ $type }}</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 capitalize">Pindah Buku Tanpa {{ ucfirst($type) }}</h2>
        <p class="text-slate-500 text-sm">
            Buku tanpa {{ $type }} setelah hapus. Pilih {{ $type }} baru untuk buku yatim.
        </p>
    </div>
    <a href="{{ route('admin.master.index', $type) }}" class="inline-flex items-center justify-center gap-2 border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold px-5 py-2.5 rounded-lg text-sm shadow-sm transition">
        ← Kembali ke Master
    </a>
</div>

@if ($bukus->total() === 0)
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
        <svg class="w-12 h-12 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-green-800 mb-2">Semua buku sudah memiliki {{ $type }}</h3>
        <p class="text-green-700">Tidak ada buku yatim yang perlu dipindah.</p>
        <a href="{{ route('admin.master.index', $type) }}" class="mt-4 inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm shadow-sm transition">
            Lanjut ke Master {{ ucfirst($type) }}
        </a>
    </div>
@else
    <!-- Bulk Reassign -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 mb-8">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Pindah Semua ({{ $bukus->total() }} buku)</h3>
        <p class="text-slate-600 mb-6">Pindahkan semua buku ke satu {{ $type }}.</p>

                    <form action="{{ route('admin.master.reassign.bulk', $type) }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                        @csrf
                        <div class="flex-1">
                            <label for="new_id_bulk" class="block text-sm font-medium text-slate-700 mb-2">Pilih {{ ucfirst($type) }} baru</label>
                            <select id="new_id_bulk" name="new_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none">
                                <option value="">— Pilih {{ ucfirst($type) }} —</option>
                                @foreach ($options as $opt)
                                    <option value="{{ $opt->id }}">{{ $opt->nama }}</option>
                                @endforeach
                            </select>
                            @error('new_id')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="self-end">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cyan-700 hover:bg-cyan-800 text-white font-semibold px-6 py-2.5 rounded-lg text-sm shadow-sm transition">
                                Pindahkan Semua ({{ $bukus->total() }} buku)
                            </button>
                        </div>
                    </form>
    </div>

    <!-- Per‑item Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Pilih Buku Satu‑per‑Satu</h3>
            <p class="text-slate-600 text-sm">Atur {{ $type }} untuk setiap buku.</p>
        </div>

        @if ($bukus->total() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Judul Buku</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Penerbit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($bukus as $buku)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-900">{{ $buku->judul }}</div>
                                    <div class="text-xs text-slate-500">ISBN: {{ $buku->isbn ?? '–' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $buku->penulis?->nama ?? '–' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $buku->penerbit?->nama ?? '–' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $buku->kategori?->nama ?? '–' }}</td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.master.reassign.item', $type) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                                        <select name="new_id" class="px-3 py-1.5 border border-slate-300 rounded text-sm outline-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                                            <option value="">— Pilih —</option>
                                            @foreach ($options as $opt)
                                                <option value="{{ $opt->id }}">{{ $opt->nama }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="inline-flex items-center justify-center gap-1 bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold px-3 py-1.5 rounded shadow-sm transition">
                                            Pindah
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $bukus->links() }}
            </div>
        @endif
    </div>
@endif
@endsection