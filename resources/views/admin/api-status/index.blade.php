@extends('admin.layouts.app')

@section('title', 'Status API')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <header>
            <p class="text-sm font-semibold text-cyan-700">Sistem</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Status API</h1>
            <p class="mt-2 text-sm text-slate-600">Pemeriksaan koneksi backend untuk aplikasi mobile.</p>
        </header>

        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">API</p>
                <p class="mt-2 text-lg font-semibold {{ $databaseOk ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $databaseOk ? 'API aktif' : 'API bermasalah' }}
                </p>
                <p class="mt-1 break-all text-xs text-slate-500">{{ $apiUrl }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Database</p>
                <p class="mt-2 text-lg font-semibold {{ $databaseOk ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $databaseOk ? 'Database terhubung' : 'Database tidak tersedia' }}
                </p>
                <p class="mt-1 text-xs text-slate-500">{{ $bookCount }} buku terdaftar</p>
            </div>
        </section>

        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-600">Diperiksa: {{ $checkedAt->format('d M Y, H:i:s') }}</p>
            <a href="{{ route('admin.status-api') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:ring-offset-2">Periksa ulang</a>
        </div>
    </div>
@endsection
