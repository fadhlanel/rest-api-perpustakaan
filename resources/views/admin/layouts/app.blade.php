<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PustakaDigital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex flex-nowrap min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden w-64 shrink-0 bg-white border-r border-slate-200 md:flex md:flex-col">
            <div class="p-6">
                <h1 class="text-xl font-bold text-slate-800">PustakaDigital</h1>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">SIPUSTAKA SMK / SMA</p>
                <div class="mt-4 px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded inline-block">UNIT PERPUSTAKAAN INDUK</div>
            </div>

            <nav class="flex-1 px-4 space-y-1">
                <div class="py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Ringkasan</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <div class="py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Master Data</div>
                <a href="{{ route('admin.buku.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.buku.*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Katalog Buku</span>
                </a>
                <a href="{{ route('admin.master.index', 'kategori') }}" class="flex items-center gap-3 px-3 py-1.5 text-xs font-medium rounded-md {{ request()->is('admin/master/kategori*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50' }}">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('admin.master.index', 'penulis') }}" class="flex items-center gap-3 px-3 py-1.5 text-xs font-medium rounded-md {{ request()->is('admin/master/penulis*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50' }}">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Penulis</span>
                </a>
                <a href="{{ route('admin.master.index', 'penerbit') }}" class="flex items-center gap-3 px-3 py-1.5 text-xs font-medium rounded-md {{ request()->is('admin/master/penerbit*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50' }}">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Penerbit</span>
                </a>

                <div class="py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Sirkulasi / Transaksi</div>
                <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.peminjaman.*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Peminjaman & Kembali</span>
                </a>

                <div class="py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Anggota & Informasi</div>
                <a href="{{ route('admin.anggota.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.anggota.*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Data Siswa / Anggota</span>
                </a>
                <a href="{{ route('admin.permintaan.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.permintaan.*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Persetujuan Profil</span>
                </a>
                <a href="{{ route('admin.pengumuman.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pengumuman.*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span>Pengumuman</span>
                </a>

                <div class="py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Sistem</div>
                <a href="{{ route('admin.profil.edit') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.profil.*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Profil Admin</span>
                </a>
                <a href="{{ route('admin.status-api') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.status-api') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8m-8 4h5m7-1a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Status API</span>
                </a>
                <a href="{{ route('admin.pengaturan.edit') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pengaturan.*') ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan Sistem</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-100">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <header class="min-h-16 bg-white border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 px-4 py-3 md:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.profil.edit') }}" class="flex items-center gap-3 group text-right">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-cyan-700 transition">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500">Administrator</p>
                        </div>
                        <div class="w-8 h-8 bg-cyan-700 text-white rounded-full flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </a>
                </div>
            </header>

            <div class="p-4 md:p-8">
                @foreach(['success' => 'green', 'error' => 'red'] as $type => $color)
                    @if(session($type))
                        <div class="mb-4 p-4 bg-{{ $color }}-50 text-{{ $color }}-700 rounded-lg text-sm border border-{{ $color }}-100" role="alert">
                            {{ session($type) }}
                        </div>
                    @endif
                @endforeach

                @yield('content')
            </div>
        </main>
    </div>
    <dialog id="confirmDialog" class="fixed inset-0 m-auto max-w-[min(90vw,400px)] rounded-xl p-0 shadow-xl backdrop:bg-slate-900/40">
        <div class="w-[min(90vw,400px)] bg-white p-6"><h2 class="text-lg font-bold text-slate-900">Konfirmasi tindakan</h2><p id="confirmMessage" class="mt-2 text-sm text-slate-600"></p><div class="mt-6 flex justify-end gap-3"><button type="button" onclick="document.getElementById('confirmDialog').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tidak</button><button type="button" id="confirmYes" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white">Ya, lanjutkan</button></div></div>
    </dialog>
    <script>document.querySelectorAll('form[data-confirm]').forEach(function(form){form.addEventListener('submit',function(event){if(form.dataset.confirmed!=='yes'){event.preventDefault();document.getElementById('confirmMessage').textContent=form.dataset.confirm;var dialog=document.getElementById('confirmDialog');dialog.showModal();document.getElementById('confirmYes').onclick=function(){form.dataset.confirmed='yes';dialog.close();form.submit()}}})})</script>
</body>
</html>
