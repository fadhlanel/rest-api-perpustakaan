<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function edit(): View
    {
        $settings = [];
        foreach (Pengaturan::DEFAULTS as $key => $default) {
            $settings[$key] = Pengaturan::get($key, $default);
        }

        return view('admin.pengaturan.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_perpustakaan' => ['required', 'string', 'max:100'],
            'nama_sekolah' => ['required', 'string', 'max:100'],
            'durasi_peminjaman' => ['required', 'integer', 'min:1', 'max:30'],
            'batas_buku' => ['required', 'integer', 'min:1', 'max:10'],
            'durasi_perpanjangan' => ['required', 'integer', 'min:1', 'max:14'],
            'status_layanan' => ['required', 'in:aktif,pemeliharaan,tutup'],
        ]);

        foreach ($data as $key => $value) {
            Pengaturan::set($key, (string) $value);
        }

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
