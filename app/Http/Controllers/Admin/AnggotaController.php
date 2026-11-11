<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.anggota.index', ['anggota' => Anggota::when($request->search, fn ($q, $s) => $q->where('nama', 'like', "%$s%")->orWhere('nis', 'like', "%$s%"))->latest()->paginate(15)]);
    }

    public function store(Request $request)
    {
        Anggota::create($request->validate(['nis' => 'required|string|max:20|unique:anggota,nis', 'nama' => 'required|string|max:255', 'kelas' => 'nullable|string|max:50', 'email' => 'nullable|email', 'telepon' => 'nullable|string|max:30']));

        return back()->with('success', 'Anggota berhasil ditambah.');
    }

    public function update(Request $request, Anggota $anggota)
    {
        $anggota->update($request->validate(['nis' => 'required|string|max:20|unique:anggota,nis,'.$anggota->id, 'nama' => 'required|string|max:255', 'kelas' => 'nullable|string|max:50', 'email' => 'nullable|email', 'telepon' => 'nullable|string|max:30']));

        return back()->with('success', 'Anggota berhasil diupdate.');
    }

    public function destroy(Anggota $anggota)
    {
        $anggota->delete();

        return back()->with('success', 'Anggota berhasil dihapus.');
    }
}
