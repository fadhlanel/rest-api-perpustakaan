<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggota::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('nis', 'like', '%'.$search.'%')
                    ->orWhere('kelas', 'like', '%'.$search.'%');
            });
        }

        $anggota = $query->latest()->paginate(15)->withQueryString();

        return view('admin.anggota.index', compact('anggota'));
    }

    public function create()
    {
        return view('admin.anggota.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nis' => 'required|string|max:20|unique:anggota,nis', 'nama' => 'required|string|max:255', 'kelas' => 'nullable|string|max:50', 'email' => 'nullable|email', 'telepon' => 'nullable|string|max:30', 'pin' => 'required|string|min:4|max:6']);
        $data['pin'] = Crypt::encryptString($data['pin']);
        Anggota::create($data);

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil ditambah.');
    }

    public function update(Request $request, Anggota $anggota)
    {
        $data = $request->validate(['nis' => 'required|string|max:20|unique:anggota,nis,'.$anggota->id, 'nama' => 'required|string|max:255', 'kelas' => 'nullable|string|max:50', 'email' => 'nullable|email', 'telepon' => 'nullable|string|max:30', 'pin' => 'nullable|string|min:4|max:6']);
        if (! empty($data['pin'])) {
            $data['pin'] = Crypt::encryptString($data['pin']);
        } else {
            unset($data['pin']);
        }
        $anggota->update($data);

        return back()->with('success', 'Anggota berhasil diupdate.');
    }

    public function destroy(Anggota $anggota)
    {
        // Menghapus baris anggota otomatis membatalkan api_token_hash miliknya,
        // sehingga request API berikutnya dari aplikasi siswa menerima 401.
        $anggota->delete();

        return back()->with('success', 'Anggota berhasil dihapus.');
    }

    public function showPin(Anggota $anggota)
    {
        $pin = null;
        if ($anggota->pin) {
            try {
                $pin = Crypt::decryptString($anggota->pin);
            } catch (\Exception $e) {
                // PIN lama masih bcrypt hash, tidak bisa decrypt
                $pin = '[Hash lama - reset PIN untuk melihat]';
            }
        }

        return response()->json([
            'nis' => $anggota->nis,
            'nama' => $anggota->nama,
            'pin' => $pin,
        ]);
    }

    public function resetPin(Request $request, Anggota $anggota)
    {
        $anggota->update([
            'pin' => null,
            'pin_reset_at' => now(),
        ]);

        return back()->with('success', 'PIN berhasil direset. Siswa harus membuat PIN baru saat login di aplikasi Android.');
    }

    public function show(Anggota $anggota)
    {
        $aktif = $anggota->peminjaman()
            ->whereNull('tanggal_dikembalikan')
            ->with('buku')
            ->latest()
            ->get();

        $selesai = $anggota->peminjaman()
            ->whereNotNull('tanggal_dikembalikan')
            ->with(['buku', 'ulasan'])
            ->latest()
            ->get();

        $denda = $anggota->peminjaman()
            ->whereNotNull('tanggal_dikembalikan')
            ->where('denda', '>', 0)
            ->with('buku')
            ->latest()
            ->get();

        $ratings = $anggota->ulasan()
            ->with('buku')
            ->latest()
            ->get();

        return view('admin.anggota.show', compact(
            'anggota',
            'aktif',
            'selesai',
            'denda',
            'ratings'
        ));
    }
}
