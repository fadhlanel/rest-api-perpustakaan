<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function __construct(private PushNotificationService $pushService) {}

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
        $pengumuman = Pengumuman::create($request->validate(['judul' => 'required|string|max:255', 'isi' => 'required|string', 'aktif' => 'boolean', 'diterbitkan_pada' => 'nullable|date']));

        if ($pengumuman->aktif) {
            $this->pushService->sendToAll(
                'Pengumuman Baru',
                $pengumuman->judul,
                ['type' => 'pengumuman', 'id' => $pengumuman->id]
            );
        }

        return back()->with('success', 'Pengumuman berhasil ditambah.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $wasInactive = !$pengumuman->aktif;
        $pengumuman->update($request->validate(['judul' => 'required|string|max:255', 'isi' => 'required|string', 'aktif' => 'boolean', 'diterbitkan_pada' => 'nullable|date']));

        if ($wasInactive && $pengumuman->aktif) {
            $this->pushService->sendToAll(
                'Pengumuman Baru',
                $pengumuman->judul,
                ['type' => 'pengumuman', 'id' => $pengumuman->id]
            );
        }

        return back()->with('success', 'Pengumuman berhasil diupdate.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
