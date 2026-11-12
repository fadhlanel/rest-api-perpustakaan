<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with(['kategori', 'penulis', 'penerbit']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($query) use ($search) {
                $query->where('judul', 'like', '%'.$search.'%')
                    ->orWhere('isbn', 'like', '%'.$search.'%')
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('penulis', function ($q) use ($search) {
                        $q->where('nama', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('penerbit', function ($q) use ($search) {
                        $q->where('nama', 'like', '%'.$search.'%');
                    });
            });
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

        $stokDipinjam = $buku->stok - $buku->stok_tersedia;
        if ($data['stok'] < $stokDipinjam) {
            throw ValidationException::withMessages([
                'stok' => "Stok minimal {$stokDipinjam} karena masih ada buku yang dipinjam.",
            ]);
        }

        $data['stok_tersedia'] = $data['stok'] - $stokDipinjam;

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

    /**
     * Sajikan file sampul langsung dari disk publik tanpa bergantung pada
     * symlink public/storage yang hilang saat container di-rebuild.
     */
    public function sampul(Buku $buku)
    {
        if (! $buku->sampul || ! Storage::disk('public')->exists($buku->sampul)) {
            abort(404);
        }

        return Storage::disk('public')->response($buku->sampul);
    }

    public function export()
    {
        $buku = Buku::with(['kategori', 'penulis', 'penerbit'])->get();
        $fileName = 'buku_export_'.date('YmdHis').'.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');

        fputcsv($handle, ['Judul', 'ISBN', 'Kategori', 'Penulis', 'Penerbit', 'Stok', 'Stok Tersedia']);

        foreach ($buku as $item) {
            fputcsv($handle, [
                $item->judul,
                $item->isbn,
                $item->kategori->nama,
                $item->penulis->nama,
                $item->penerbit->nama,
                $item->stok,
                $item->stok_tersedia,
            ]);
        }

        fclose($handle);
        exit;
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        fgetcsv($handle); // lewati baris header

        $berhasil = 0;
        $gagal = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            [$judul, $isbn, $kategoriNama, $penulisNama, $penerbitNama, $stok] = array_pad($row, 6, null);

            if (blank($judul)) {
                $gagal++;

                continue;
            }

            $kategori = Kategori::firstOrCreate(['nama' => trim($kategoriNama ?? '')]);
            $penulis = Penulis::firstOrCreate(['nama' => trim($penulisNama ?? '')]);
            $penerbit = Penerbit::firstOrCreate(['nama' => trim($penerbitNama ?? '')]);

            $stokBaru = max(0, (int) ($stok ?? 0));

            Buku::create([
                'judul' => trim($judul),
                'isbn' => trim($isbn ?? ''),
                'stok' => $stokBaru,
                'stok_tersedia' => $stokBaru,
                'kategori_id' => $kategori->id,
                'penulis_id' => $penulis->id,
                'penerbit_id' => $penerbit->id,
            ]);

            $berhasil++;
        }

        fclose($handle);

        return back()->with('success', "Import CSV selesai. Berhasil: {$berhasil}, gagal: {$gagal}.");
    }
}
