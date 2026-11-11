@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
    <p class="text-slate-500 text-sm">Status operasional perpustakaan hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase">Total Judul</p>
        <h3 class="text-3xl font-bold mt-1">{{ number_format($stats['total_buku']) }}</h3>
        <p class="text-[10px] text-green-600 mt-2 font-medium">Buku terdaftar</p>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase">Total Eksemplar</p>
        <h3 class="text-3xl font-bold mt-1">{{ number_format($stats['total_eksemplar']) }}</h3>
        <p class="text-[10px] text-blue-600 mt-2 font-medium">{{ number_format($stats['tersedia']) }} tersedia di rak</p>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase">Sedang Dipinjam</p>
        <h3 class="text-3xl font-bold mt-1">{{ number_format($stats['dipinjam']) }}</h3>
        <p class="text-[10px] text-orange-600 mt-2 font-medium">Aktif siswa</p>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase">Status DB</p>
        <h3 class="text-xl font-bold mt-2 {{ $stats['database_ok'] ? 'text-green-600' : 'text-red-600' }}">
            {{ $stats['database_ok'] ? 'Connected' : 'Error' }}
        </h3>
        <p class="text-[10px] text-slate-500 mt-2 font-medium">Neon PostgreSQL</p>
    </div>
</div>
@endsection
