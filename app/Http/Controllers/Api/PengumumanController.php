<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;

class PengumumanController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(Pengumuman::where('aktif', true)->latest()->get(), 'Daftar pengumuman.');
    }

    public function show(Pengumuman $pengumuman): JsonResponse
    {
        abort_unless($pengumuman->aktif, 404);

        return $this->successResponse($pengumuman, 'Detail pengumuman.');
    }
}
