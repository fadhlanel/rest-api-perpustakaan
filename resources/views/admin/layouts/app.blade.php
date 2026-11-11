<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PustakaDigital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden w-64 shrink-0 bg-white border-r border-slate-200 md:flex md:flex-col">
            <div class="p-6">
                <h1 class="text-xl font-bold text-slate-800">PustakaDigital</h1>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">SIPUSTAKA SMK / SMA</p>
                <div class="mt-4 px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded inline-block">UNIT PERPUSTAKAAN INDUK</div>
            </div>

            <nav class="flex-1 px-4 space-y-1">
                <div class="py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Ringkasan</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    Dashboard
                </a>

                <div class="py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Master Data</div>
                <a href="{{ route('admin.buku.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.buku.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    Katalog & Master Buku
                </a>

                <div class="py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3">Sirkulasi</div>
                <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.peminjaman.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    Peminjaman Buku
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
                <div class="flex items-center bg-slate-100 px-3 py-1.5 rounded-lg w-full md:w-96">
                    <input type="search" aria-label="Cari panel admin" placeholder="Cari..." class="bg-transparent border-none focus:ring-0 text-sm w-full">
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-500">Administrator</p>
                    </div>
                    <div class="w-8 h-8 bg-slate-200 rounded-full"></div>
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
</body>
</html>
