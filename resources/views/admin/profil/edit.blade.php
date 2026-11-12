@extends('admin.layouts.app')

@section('title', 'Profil Administrator')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Profil Administrator</h2>
    <p class="text-slate-500 text-sm">Kelola informasi akun dan kata sandi login panel admin perpustakaan.</p>
</div>

<div class="max-w-2xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <form action="{{ route('admin.profil.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500">
            @error('email')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 mb-1">Ubah Kata Sandi</h3>
            <p class="text-xs text-slate-400 mb-4">Kosongkan jika tidak ingin mengubah kata sandi saat ini.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Kata Sandi Baru</label>
                    <input type="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="bg-cyan-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-cyan-800 shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
