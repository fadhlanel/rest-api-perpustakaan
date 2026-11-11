<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PustakaDigital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @theme {
            --color-primary: #1e293b;
            --color-sidebar: #f8fafc;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-primary">PustakaDigital</h1>
            <p class="text-gray-500 text-sm">SIPUSTAKA SMK / SMA</p>
        </div>
        
        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2 border" required>
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2 border" required>
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-md hover:bg-slate-800 transition">Login</button>
        </form>
    </div>
</body>
</html>
