<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20'],
            'pin' => ['required', 'string', 'max:255'],
        ]);
        $anggota = Anggota::where('nis', $data['nis'])->first();

        if (! $anggota || ! Hash::check($data['pin'], $anggota->pin ?? '')) {
            return $this->errorResponse('NIS atau PIN salah.', 401);
        }

        $token = Str::random(80);
        $anggota->forceFill(['api_token_hash' => hash('sha256', $token)])->save();

        return $this->successResponse(['token' => $token, 'siswa' => $this->profileData($anggota)], 'Login berhasil.');
    }

    public function profile(Request $request): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);

        return $anggota
            ? $this->successResponse(['siswa' => $this->profileData($anggota)], 'Profil siswa.')
            : $this->errorResponse('Token tidak valid.', 401);
    }

    private function authenticatedStudent(Request $request): ?Anggota
    {
        $token = $request->bearerToken();

        return $token ? Anggota::where('api_token_hash', hash('sha256', $token))->first() : null;
    }

    private function profileData(Anggota $anggota): array
    {
        return $anggota->only(['id', 'nis', 'nama', 'kelas', 'email', 'telepon']);
    }
}
