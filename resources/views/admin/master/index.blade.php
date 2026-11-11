@extends('admin.layouts.app')
@section('title', ucfirst($type))
@section('content')
<h2 class="text-2xl font-bold mb-6">Master {{ ucfirst($type) }}</h2>
<form method="POST" action="{{ route('admin.master.store', $type) }}" class="bg-white p-4 mb-6 flex gap-2">@csrf<input name="nama" required placeholder="Nama {{ $type }}" class="border px-3 py-2 flex-1"><button class="bg-blue-600 text-white px-4 py-2">Tambah</button></form>
<div class="bg-white"><table class="w-full text-left"><tbody>@forelse($items as $item)<tr class="border-b"><td class="p-4">{{ $item->nama }}</td><td class="p-4 text-right"><form method="POST" action="{{ route('admin.master.destroy', [$type, $item->id]) }}">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form></td></tr>@empty<tr><td class="p-4">Belum ada data.</td></tr>@endforelse</tbody></table></div>{{ $items->links() }}
@endsection
