<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileEditRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermintaanEditController extends Controller
{
    public function index(): View
    {
        $permintaan = ProfileEditRequest::with(['anggota', 'reviewer'])
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [ProfileEditRequest::STATUS_PENDING])
            ->latest()
            ->paginate(15);

        return view('admin.permintaan.index', compact('permintaan'));
    }

    public function approve(ProfileEditRequest $permintaan): RedirectResponse
    {
        abort_unless($permintaan->status === ProfileEditRequest::STATUS_PENDING, 404);

        $anggota = $permintaan->anggota;

        // Hanya field yang diizinkan yang boleh diterapkan dari payload siswa.
        $perubahan = array_intersect_key(
            $permintaan->perubahan ?? [],
            array_flip(['nis', 'nama', 'kelas', 'email', 'telepon'])
        );

        $anggota->fill($perubahan)->save();

        $permintaan->update([
            'status' => ProfileEditRequest::STATUS_APPROVED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Perubahan profil '.$anggota->nama.' disetujui dan diterapkan.');
    }

    public function reject(ProfileEditRequest $permintaan): RedirectResponse
    {
        abort_unless($permintaan->status === ProfileEditRequest::STATUS_PENDING, 404);

        $permintaan->update([
            'status' => ProfileEditRequest::STATUS_REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Permintaan perubahan profil ditolak.');
    }
}
