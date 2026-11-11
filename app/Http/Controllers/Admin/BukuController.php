<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penulis;
use App\Models\Penerbit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with(['kategori', 'penulis', 'penerbit']);

        if ($request->search) {
            $query->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('isbn', 'like', '%' . $request->search . '%');
        }

        $buku = $query->latest()->paginate(10);
        return view('admin.buku.index', compact('buku'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        $penulis = Penulis::all();
        $penerbit = Penerbit::all();
        return view('admin.buku.form', compact('kategori', 'penulis', 'penerbit'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_terbit' => 'required|integer',
            'isbn' => 'nullable|string|max:30',
            'stok' => 'required|integer|min:0',
            'kategori_id' => 'required|exists:kategori,id',
            'penulis_id' => 'required|exists:penulis,id',
            'penerbit_id' => 'required|exists:penerbit,id',
            'sampul' => 'nullable|image|max:2048',
        ]);

        $data['stok_tersedia'] = $data['stok'];

        if ($request->hasFile('sampul')) {
            $data['sampul'] = $request->file('sampul')->store('sampul', 'public');
        }

        Buku::create($data);
        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil ditambah.');
    }

    public function edit(Buku $buku)
    {
        $kategori = Kategori::all();
        $penulis = Penulis::all();
        $penerbit = Penerbit::all();
        return view('admin.buku.form', compact('buku', 'kategori', 'penulis', 'penerbit'));
    }

    public function update(Request $request, Buku $buku)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_terbit' => 'required|integer',
            'isbn' => 'nullable|string|max:30',
            'stok' => 'required|integer|min:0',
            'kategori_id' => 'required|exists:kategori,id',
            'penulis_id' => 'required|exists:penulis,id',
            'penerbit_id' => 'required|exists:penerbit,id',
            'sampul' => 'nullable|image|max:2048',
        ]);

        // Hitung selisih stok untuk update stok_tersedia
        $selisih = $data['stok'] - $buku->stok;
        $data['stok_tersedia'] = $buku->stok_tersedia + $selisih;

        if ($request->hasFile('sampul')) {
            if ($buku->sampul) {
                Storage::disk('public')->delete($buku->sampul);
            }
            $data['sampul'] = $request->file('sampul')->store('sampul', 'public');
        }

        $buku->update($data);
        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil diupdate.');
    }

    public function destroy(Buku $buku)
    {
        if ($buku->sampul) {
            Storage::disk('public')->delete($buku->sampul);
        }
        $buku->delete();
        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
