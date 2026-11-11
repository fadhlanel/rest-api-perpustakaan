<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin | PustakaDigital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900">
    <main class="mx-auto flex min-h-screen w-full max-w-md items-center px-5 py-10">
        <section class="w-full border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="login-title">
            <header class="border-b border-slate-200 pb-5">
                <p class="text-sm font-semibold text-sky-800">PustakaDigital</p>
                <h1 id="login-title" class="mt-2 text-2xl font-bold tracking-tight text-slate-950">Masuk ke panel admin</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Gunakan akun petugas perpustakaan yang terdaftar.</p>
            </header>

            @if ($errors->any())
                <div class="mt-5 border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-800">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" class="mt-2 block min-h-11 w-full border border-slate-300 bg-white px-3 text-base text-slate-950 shadow-sm outline-none transition focus:border-sky-700 focus:ring-2 focus:ring-sky-200" required autofocus>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-800">Password</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" class="mt-2 block min-h-11 w-full border border-slate-300 bg-white px-3 text-base text-slate-950 shadow-sm outline-none transition focus:border-sky-700 focus:ring-2 focus:ring-sky-200" required>
                </div>

                <button type="submit" class="min-h-11 w-full bg-sky-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-900 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2 active:bg-sky-950">
                    Masuk ke panel
                </button>
            </form>
        </section>
    </main>
</body>
</html>
