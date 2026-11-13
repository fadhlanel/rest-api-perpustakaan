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

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Log request API</h2>
                <p class="mt-1 text-xs text-slate-500">Maksimal 20 request terbaru. Token dan kredensial tidak dicatat.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Waktu</th><th class="px-5 py-3">Method</th><th class="px-5 py-3">Path</th><th class="px-5 py-3">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-3 text-slate-600"><time>{{ $log['time'] ?? '-' }}</time></td>
                                <td class="px-5 py-3"><span class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-bold text-cyan-700">{{ $log['method'] ?? '-' }}</span></td>
                                <td class="max-w-[28rem] truncate px-5 py-3 font-mono text-xs text-slate-600" title="{{ $log['path'] ?? '-' }}">{{ $log['path'] ?? '-' }}</td>
                                @php($status = (int) ($log['status'] ?? 500))
                                <td class="px-5 py-3 font-semibold {{ $status < 300 ? 'text-emerald-700' : ($status < 400 ? 'text-slate-600' : ($status < 500 ? 'text-amber-700' : 'text-red-700')) }}">{{ $status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada request API.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
