<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        return view('admin.pengumuman.index', ['pengumuman' => Pengumuman::latest()->paginate(15)]);
    }

    public function store(Request $request)
    {
        Pengumuman::create($request->validate(['judul' => 'required|string|max:255', 'isi' => 'required|string', 'aktif' => 'boolean', 'diterbitkan_pada' => 'nullable|date']));

        return back()->with('success', 'Pengumuman berhasil ditambah.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $pengumuman->update($request->validate(['judul' => 'required|string|max:255', 'isi' => 'required|string', 'aktif' => 'boolean', 'diterbitkan_pada' => 'nullable|date']));

        return back()->with('success', 'Pengumuman berhasil diupdate.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
