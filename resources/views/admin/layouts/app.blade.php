<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PustakaDigital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <div class="flex flex-nowrap min-h-screen">
        <!-- Desktop Sidebar -->
        <aside class="hidden w-64 shrink-0 bg-white border-r border-slate-200 md:flex md:flex-col">
            <div class="p-6">
                <h1 class="text-xl font-bold text-slate-800">PustakaDigital</h1>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">SIPUSTAKA SMK / SMA</p>
                <div class="mt-4 px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded inline-block">UNIT PERPUSTAKAAN INDUK</div>
            </div>

            @include('admin.layouts.partials.sidebar-nav')

            <div class="p-4 border-t border-slate-100">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div id="mobileSidebarBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none md:hidden" aria-hidden="true"></div>

        <!-- Mobile Off-Canvas Sidebar Drawer -->
        <aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white shadow-2xl flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden" aria-label="Mobile Navigation">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">PustakaDigital</h2>
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider">SIPUSTAKA SMK / SMA</p>
                </div>
                <button type="button" onclick="toggleMobileSidebar()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition" aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-2">
                @include('admin.layouts.partials.sidebar-nav')
            </div>

            <div class="p-4 border-t border-slate-100">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0 flex flex-col overflow-y-auto">
            <header class="min-h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 py-3 md:px-8 sticky top-0 z-20">
                <!-- Mobile Menu Button & Brand -->
                <div class="flex items-center gap-3 md:hidden">
                    <button type="button" onclick="toggleMobileSidebar()" class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" aria-label="Buka menu navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="font-bold text-slate-800 text-base">PustakaDigital</span>
                </div>

                <div class="hidden md:block">
                    <!-- Ruang kosong desktop header kiri -->
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.profil.edit') }}" class="flex items-center gap-3 group text-right">
                        <div class="hidden sm:block">
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-cyan-700 transition">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500">Administrator</p>
                        </div>
                        <div class="w-8 h-8 bg-cyan-700 text-white rounded-full flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </a>
                </div>
            </header>

            <div class="p-4 sm:p-6 lg:p-8 flex-1">
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

    <!-- Confirm Dialog -->
    <dialog id="confirmDialog" class="fixed inset-0 m-auto max-w-[min(90vw,400px)] rounded-xl p-0 shadow-xl backdrop:bg-slate-900/40">
        <div class="w-[min(90vw,400px)] bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Konfirmasi tindakan</h2>
            <p id="confirmMessage" class="mt-2 text-sm text-slate-600"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('confirmDialog').close()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Tidak</button>
                <button type="button" id="confirmYes" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Ya, lanjutkan</button>
            </div>
        </div>
    </dialog>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const backdrop = document.getElementById('mobileSidebarBackdrop');
            if (!sidebar || !backdrop) return;
            
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                document.body.classList.remove('overflow-hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100', 'pointer-events-auto');
                document.body.classList.add('overflow-hidden');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('mobileSidebar');
                if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    toggleMobileSidebar();
                }
            }
        });

        document.querySelectorAll('form[data-confirm]').forEach(function(form){
            form.addEventListener('submit', function(event){
                if(form.dataset.confirmed !== 'yes'){
                    event.preventDefault();
                    document.getElementById('confirmMessage').textContent = form.dataset.confirm;
                    var dialog = document.getElementById('confirmDialog');
                    dialog.showModal();
                    document.getElementById('confirmYes').onclick = function(){
                        form.dataset.confirmed = 'yes';
                        dialog.close();
                        form.submit();
                    };
                }
            });
        });
    </script>
</body>
</html>