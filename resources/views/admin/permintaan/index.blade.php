@extends('admin.layouts.app')

@section('title', 'Persetujuan Profil')

@section('content')
<div class="mb-8"><h2 class="text-2xl font-bold text-slate-800">Persetujuan Perubahan Profil</h2><p class="mt-1 text-sm text-slate-500">Periksa data yang diajukan siswa sebelum diterapkan.</p></div>
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left">
        <thead class="border-b border-slate-100 bg-slate-50"><tr><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Siswa</th><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Perubahan</th><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th><th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse($permintaan as $item)
            <tr>
                <td class="px-6 py-4 align-top"><p class="font-semibold text-slate-800">{{ $item->anggota->nama }}</p><p class="mt-1 text-xs text-slate-500">NIS {{ $item->anggota->nis }}</p><p class="mt-1 text-xs text-slate-400">{{ $item->created_at->format('d M Y H:i') }}</p></td>
                <td class="px-6 py-4 align-top"><dl class="space-y-1 text-sm">@foreach($item->perubahan as $field => $value)<div class="flex gap-2"><dt class="w-16 font-semibold text-slate-500">{{ ucfirst($field) }}</dt><dd class="text-slate-800">{{ $value ?: '(kosong)' }}</dd></div>@endforeach</dl></td>
                <td class="px-6 py-4 align-top"><span class="rounded px-2 py-1 text-xs font-semibold {{ ['pending' => 'bg-amber-50 text-amber-700', 'disetujui' => 'bg-emerald-50 text-emerald-700', 'ditolak' => 'bg-red-50 text-red-700'][$item->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($item->status) }}</span></td>
                <td class="px-6 py-4 text-right align-top">
                    @if($item->status === 'pending')<div class="flex justify-end gap-2"><form method="POST" action="{{ route('admin.permintaan.approve', $item) }}">@csrf<button class="rounded bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Setujui</button></form><form method="POST" action="{{ route('admin.permintaan.reject', $item) }}">@csrf<button class="rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Tolak</button></form></div>@else<span class="text-xs text-slate-400">{{ $item->reviewer?->name ?? 'Admin' }}</span>@endif
                </td>
            </tr>
        @empty <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada permintaan perubahan profil.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="border-t border-slate-100 p-6">{{ $permintaan->links() }}</div>
</div>
@endsection
