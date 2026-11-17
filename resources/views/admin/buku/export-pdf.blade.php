@extends('admin.layouts.app')

@section('title', 'Cetak Label Buku')

@push('styles')
<style>
/* ── Toolbar (sembunyikan saat cetak) ── */
.no-print { display: block; }

@media print {
    .no-print { display: none !important; }

    /* Sembunyikan sidebar & header layout admin */
    #sidebar, #topbar, #adminSidebar, .admin-sidebar,
    nav, header, aside { display: none !important; }

    /* Konten utama full-width */
    main, #main-content, .main-content, [id*="content"] {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: none !important;
    }

    body { background: #fff !important; }

    /* Grid label A4 – 2 kolom, 5 baris = 10 label per halaman */
    #label-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr);
        gap: 6mm;
        padding: 10mm;
        width: 190mm;
        margin: 0 auto;
    }

    .label-card {
        border: 1px solid #334155 !important;
        border-radius: 4px;
        padding: 6px 10px;
        font-family: 'Courier New', monospace;
        font-size: 9pt;
        page-break-inside: avoid;
        min-height: 44mm;
        display: flex !important;
        flex-direction: column;
        justify-content: space-between;
    }

    .label-card .dewey { font-size: 15pt; font-weight: 800; }
    .label-card .judul { font-size: 8pt; font-weight: 700; margin-top: 2px; }
    .label-card .meta  { font-size: 7pt; color: #475569; }
    .label-card .isbn  { font-size: 7pt; letter-spacing: 1px; border-top: 1px solid #cbd5e1; margin-top: 4px; padding-top: 2px; }

    @page { size: A4; margin: 0; }
}

/* ── Layar (screen) ── */
@media screen {
    #label-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .label-card {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px 14px;
        font-family: 'Courier New', monospace;
        font-size: 11px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 130px;
    }

    .label-card .dewey { font-size: 1.3rem; font-weight: 800; color: #0f172a; }
    .label-card .judul { font-size: 0.75rem; font-weight: 700; color: #1e293b; margin-top: 2px; }
    .label-card .meta  { font-size: 0.65rem; color: #64748b; }
    .label-card .isbn  { font-size: 0.65rem; letter-spacing: 0.5px; border-top: 1px solid #e2e8f0;
                          margin-top: 6px; padding-top: 6px; color: #94a3b8; }
}
</style>
@endpush

@section('content')

{{-- ── Toolbar (hanya tampil di layar) ── --}}
<div class="no-print mb-6 flex flex-wrap items-center gap-3">
    <a href="{{ route('admin.buku.index') }}"
       class="flex items-center gap-1 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Katalog
    </a>

    {{-- Filter kategori --}}
    <form method="GET" action="{{ route('admin.buku.export-pdf') }}" class="flex items-center gap-2">
        <label class="text-sm text-slate-600 font-medium">Filter:</label>
        <select name="kategori_id" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            <option value="">Semua Kategori</option>
            @foreach($kategori as $k)
                <option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-2 bg-slate-700 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
            Terapkan
        </button>
    </form>

    <div class="flex-1"></div>

    {{-- Info jumlah --}}
    <span class="text-sm text-slate-500">{{ $buku->count() }} buku</span>

    {{-- Tombol cetak --}}
    <button onclick="window.print()"
            class="flex items-center gap-2 px-5 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-blue-800 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        🖨️ Cetak / Simpan PDF
    </button>
</div>

{{-- ── Judul halaman (layar) ── --}}
<div class="no-print mb-5">
    <h1 class="text-2xl font-bold text-slate-800">Label Buku Perpustakaan</h1>
    <p class="text-sm text-slate-500 mt-1">Gunakan browser "Print → Save as PDF" untuk menyimpan sebagai PDF. Layout A4 2 kolom siap cetak.</p>
</div>

@if($buku->isEmpty())
    <div class="no-print flex flex-col items-center justify-center py-20 text-slate-400">
        <svg class="w-16 h-16 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="font-semibold">Tidak ada buku ditemukan.</p>
    </div>
@else
    {{-- ── Grid Label ── --}}
    <div id="label-grid">
        @foreach($buku as $item)
        @php
            // Dewey call number: ambil 3 digit pertama dari isbn atau gunakan auto-increment index
            // Kalau ada nomor Dewey custom, gunakan itu; fallback ke kode kategori
            $dewey = str_pad($loop->iteration * 10, 3, '0', STR_PAD_LEFT);
            if ($item->isbn && strlen($item->isbn) >= 3) {
                // Ambil 3 digit terakhir ISBN sebagai pseudo call-number
                $dewey = substr(preg_replace('/[^0-9]/', '', $item->isbn), -3, 3);
                if (strlen($dewey) < 3) $dewey = str_pad($dewey, 3, '0', STR_PAD_LEFT);
            }
        @endphp
        <div class="label-card">
            <div>
                <div class="dewey">{{ $dewey }}</div>
                <div class="judul">{{ Str::limit($item->judul, 60) }}</div>
                <div class="meta">{{ $item->penulis?->nama ?? '-' }}</div>
                <div class="meta">{{ $item->penerbit?->nama ?? '-' }} {{ $item->tahun_terbit ?? '' }}</div>
                @if($item->kategori)
                    <div class="meta">
                        <span class="inline-block mt-1 px-1 py-0 rounded text-[7pt] border border-slate-300">
                            {{ $item->kategori->nama }}
                        </span>
                    </div>
                @endif
            </div>
            <div class="isbn">{{ $item->isbn ?? 'ISBN –' }} &nbsp;|&nbsp; Stok: {{ $item->stok }}</div>
        </div>
        @endforeach
    </div>
@endif

@endsection
