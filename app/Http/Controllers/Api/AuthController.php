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

    public function update(Request $request): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);

        if (! $anggota) {
            return $this->errorResponse('Token tidak valid.', 401);
        }

        $data = $request->validate([
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:anggota,email,'.$anggota->id],
            'telepon' => ['nullable', 'string', 'max:20'],
            'pin' => ['nullable', 'string', 'min:4', 'max:6'],
        ]);

        if (isset($data['nama'])) {
            $anggota->nama = $data['nama'];
        }
        if (array_key_exists('email', $data)) {
            $anggota->email = $data['email'];
        }
        if (array_key_exists('telepon', $data)) {
            $anggota->telepon = $data['telepon'];
        }
        if (! empty($data['pin'])) {
            $anggota->pin = Hash::make($data['pin']);
        }

        $anggota->save();

        return $this->successResponse(['siswa' => $this->profileData($anggota)], 'Profil berhasil diperbarui.');
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
