@php
    $namaPerpustakaan = \App\Models\Pengaturan::get('nama_perpustakaan');
    $namaSekolah = \App\Models\Pengaturan::get('nama_sekolah');
    $masterDataIssues = \App\Support\MasterDataIssues::all();
    $masterDataIssueTotal = $masterDataIssues->sum('count');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ $namaPerpustakaan }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Standalone responsive sidebar rules - immune to build/cache discrepancies */
        @media (min-width: 768px) {
            #adminSidebar {
                display: flex !important;
                position: static !important;
                transform: none !important;
                width: 16rem !important;
                flex-shrink: 0 !important;
                visibility: visible !important;
                box-shadow: none !important;
            }
            #mobileSidebarBackdrop {
                display: none !important;
                visibility: hidden !important;
            }
            #mobileMenuBtn {
                display: none !important;
            }
            #mobileSidebarCloseBtn {
                display: none !important;
            }
        }
        @media (max-width: 767.98px) {
            #adminSidebar {
                position: fixed !important;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 50;
                width: 18rem;
                max-width: 85vw;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #adminSidebar.mobile-closed {
                transform: translateX(-100%) !important;
                pointer-events: none;
            }
            #adminSidebar.mobile-open {
                transform: translateX(0) !important;
                pointer-events: auto;
            }
            #mobileSidebarBackdrop {
                transition: opacity 0.3s ease-in-out;
            }
            #mobileSidebarBackdrop.backdrop-closed {
                display: none !important;
                opacity: 0;
                pointer-events: none;
            }
            #mobileSidebarBackdrop.backdrop-open {
                display: block !important;
                opacity: 1;
                pointer-events: auto;
            }
            #mobileMenuBtn {
                display: inline-flex !important;
            }
            #mobileSidebarCloseBtn {
                display: inline-flex !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <div class="flex flex-nowrap min-h-screen">
        <!-- Unified Responsive Sidebar (Desktop docked, Mobile slide-in drawer) -->
        <aside id="adminSidebar" class="mobile-closed bg-white border-r border-slate-200 flex flex-col min-h-screen" aria-label="Navigasi Utama">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">{{ $namaPerpustakaan }}</h1>
                    <p class="text-xs text-slate-500 uppercase tracking-wider mt-0.5">{{ $namaSekolah }}</p>
                    <div class="mt-3 px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded inline-block">UNIT PERPUSTAKAAN INDUK</div>
                </div>
                <button type="button" id="mobileSidebarCloseBtn" onclick="closeMobileSidebar()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition" aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
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

        <!-- Mobile Backdrop Overlay (Only visible on mobile when drawer is opened) -->
        <div id="mobileSidebarBackdrop" onclick="closeMobileSidebar()" class="backdrop-closed fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs" aria-hidden="true"></div>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0 flex flex-col overflow-y-auto">
            <header class="min-h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 py-3 md:px-8 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button type="button" id="mobileMenuBtn" onclick="openMobileSidebar()" class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" aria-label="Buka menu navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="font-bold text-slate-800 text-base md:hidden">{{ $namaPerpustakaan }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative" id="notificationWrapper">
                        <button type="button" id="notificationBtn" onclick="toggleNotifications()" aria-expanded="false" aria-controls="notificationPanel" aria-label="Notifikasi data master" class="relative p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if ($masterDataIssueTotal > 0)
                                <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-amber-500 text-white text-[10px] font-bold leading-none" aria-label="{{ $masterDataIssueTotal }} buku perlu diperbaiki">{{ $masterDataIssueTotal }}</span>
                            @endif
                        </button>
                        <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-80 max-w-[85vw] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg z-30">
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="text-sm font-semibold text-slate-800">Notifikasi</p>
                                <p class="text-xs text-slate-500">Data master yang perlu diperbaiki.</p>
                            </div>
                            @if ($masterDataIssues->isEmpty())
                                <p class="px-4 py-6 text-center text-sm text-slate-500">Tidak ada peringatan.</p>
                            @else
                                <ul class="divide-y divide-slate-100">
                                    @foreach ($masterDataIssues as $issue)
                                        <li>
                                            <a href="{{ $issue['url'] }}" class="flex items-start gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 transition">
                                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span>{{ $issue['message'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
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
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg text-sm border border-green-100" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg text-sm border border-red-100" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800" role="alert">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="font-medium">{{ session('warning') }}</p>
                            @if(session('warning_url'))
                                <a href="{{ session('warning_url') }}" class="inline-flex items-center gap-1 font-semibold text-amber-900 underline hover:no-underline whitespace-nowrap">
                                    Perbaiki sekarang
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

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
        function openMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('mobileSidebarBackdrop');
            if (sidebar) {
                sidebar.classList.remove('mobile-closed');
                sidebar.classList.add('mobile-open');
            }
            if (backdrop) {
                backdrop.classList.remove('backdrop-closed');
                backdrop.classList.add('backdrop-open');
            }
            document.body.classList.add('overflow-hidden');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('mobileSidebarBackdrop');
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
                sidebar.classList.add('mobile-closed');
            }
            if (backdrop) {
                backdrop.classList.remove('backdrop-open');
                backdrop.classList.add('backdrop-closed');
            }
            document.body.classList.remove('overflow-hidden');
        }

        function toggleNotifications() {
            const panel = document.getElementById('notificationPanel');
            const btn = document.getElementById('notificationBtn');
            if (!panel || !btn) return;
            const willOpen = panel.classList.contains('hidden');
            panel.classList.toggle('hidden', !willOpen);
            btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        }

        function closeNotifications() {
            const panel = document.getElementById('notificationPanel');
            const btn = document.getElementById('notificationBtn');
            if (!panel || !btn) return;
            panel.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notificationWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                closeNotifications();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileSidebar();
                closeNotifications();
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