<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $data = $request->validate(['nis' => 'required|string|max:20|unique:anggota,nis', 'nama' => 'required|string|max:255', 'kelas' => 'nullable|string|max:50', 'email' => 'nullable|email', 'telepon' => 'nullable|string|max:30', 'pin' => 'required|string|min:4|max:255']);
        $data['pin'] = Hash::make($data['pin']);
        Anggota::create($data);

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil ditambah.');
    }

    public function update(Request $request, Anggota $anggota)
    {
        $data = $request->validate(['nis' => 'required|string|max:20|unique:anggota,nis,'.$anggota->id, 'nama' => 'required|string|max:255', 'kelas' => 'nullable|string|max:50', 'email' => 'nullable|email', 'telepon' => 'nullable|string|max:30', 'pin' => 'nullable|string|min:4|max:255']);
        if (! empty($data['pin'])) {
            $data['pin'] = Hash::make($data['pin']);
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
}
