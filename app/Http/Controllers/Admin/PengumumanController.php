<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        $query = Pengumuman::query();

        if (request()->filled('search')) {
            $search = request()->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%'.$search.'%')
                    ->orWhere('isi', 'like', '%'.$search.'%');
            });
        }

        if (request()->filled('aktif')) {
            $query->where('aktif', request()->boolean('aktif'));
        }

        $pengumuman = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pengumuman.index', compact('pengumuman'));
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
