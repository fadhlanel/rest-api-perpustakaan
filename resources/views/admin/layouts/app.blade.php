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

            @include('admin.layouts.partials.sidebar-nav')

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
        <main class="flex-1 min-w-0 flex flex-col overflow-y-auto">
            <header class="min-h-16 bg-white border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 px-4 py-3 md:px-8">
                <details class="md:hidden relative w-full sm:w-auto">
                    <summary class="flex items-center gap-2 px-3 py-2 rounded-md border border-slate-200 text-slate-700 text-sm font-medium cursor-pointer select-none [&::-webkit-details-marker]:hidden list-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>Menu</span>
                    </summary>
                    <div class="absolute left-0 right-0 top-full z-50 mt-2 rounded-lg border border-slate-200 bg-white shadow-lg p-2">
                        @include('admin.layouts.partials.sidebar-nav')
                        <form action="{{ route('admin.logout') }}" method="POST" class="px-1 pt-2 border-t border-slate-100">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md">Logout</button>
                        </form>
                    </div>
                </details>
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
    <script>
        document.querySelectorAll('form[data-confirm]').forEach(function(form){
            form.addEventListener('submit',function(event){
                if(form.dataset.confirmed!=='yes'){
                    event.preventDefault();
                    document.getElementById('confirmMessage').textContent=form.dataset.confirm;
                    var dialog=document.getElementById('confirmDialog');
                    dialog.showModal();
                    document.getElementById('confirmYes').onclick=function(){
                        form.dataset.confirmed='yes';
                        dialog.close();
                        form.submit()
                    }
                }
            })
        })
        document.querySelectorAll('details[open] > div a, details[open] > div button').forEach(function(el){
            el.addEventListener('click',function(){var d=el.closest('details');if(d){d.removeAttribute('open');}})
        })
    </script>
</body>
</html>
