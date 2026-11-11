@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-7">
    <p class="text-sm font-semibold text-sky-800">Ringkasan perpustakaan</p>
    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Dashboard</h1>
    <p class="mt-2 text-sm text-slate-600">Pantau koleksi dan peminjaman yang sedang berjalan.</p>
</div>

@if (! $stats['database_ok'])
    <div class="mb-6 border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">
        Data dashboard tidak dapat dimuat karena koneksi database bermasalah. Periksa konfigurasi database lalu muat ulang halaman.
    </div>
@else
    <section aria-label="Statistik perpustakaan" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-600">Judul buku</h2>
            <p class="mt-3 text-3xl font-bold tabular-nums text-slate-950">{{ number_format($stats['total_buku']) }}</p>
            <p class="mt-2 text-sm text-slate-600">Koleksi terdaftar</p>
        </article>

        <article class="border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-600">Total eksemplar</h2>
            <p class="mt-3 text-3xl font-bold tabular-nums text-slate-950">{{ number_format($stats['total_eksemplar']) }}</p>
            <p class="mt-2 text-sm text-slate-600">{{ number_format($stats['tersedia']) }} tersedia</p>
        </article>

        <article class="border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-600">Sedang dipinjam</h2>
            <p class="mt-3 text-3xl font-bold tabular-nums text-slate-950">{{ number_format($stats['dipinjam']) }}</p>
            <p class="mt-2 text-sm text-slate-600">Peminjaman aktif</p>
        </article>

        <article class="border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-600">Koneksi database</h2>
            <p class="mt-3 text-xl font-bold text-emerald-800">Terhubung</p>
            <p class="mt-2 text-sm text-slate-600">Neon PostgreSQL</p>
        </article>
    </section>
@endif
@endsection
