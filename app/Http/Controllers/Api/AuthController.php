<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\ProfileEditRequest;
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

    /**
     * Perubahan profil siswa tidak langsung diterapkan: disimpan sebagai
     * permintaan dan baru berlaku setelah admin menyetujuinya.
     */
    public function update(Request $request): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);

        if (! $anggota) {
            return $this->errorResponse('Token tidak valid.', 401);
        }

        $data = $request->validate([
            'nis' => ['sometimes', 'required', 'string', 'max:20', 'unique:anggota,nis,'.$anggota->id],
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'kelas' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:anggota,email,'.$anggota->id],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $perubahan = collect($data)->filter(
            fn ($value, $field) => trim((string) $value) !== trim((string) $anggota->{$field})
        )->map(fn ($value) => trim((string) $value))->all();

        if ($perubahan === []) {
            return $this->successResponse(
                ['siswa' => $this->profileData($anggota), 'permintaan_pending' => $this->pendingCount($anggota)],
                'Tidak ada perubahan data profil.'
            );
        }

        ProfileEditRequest::create([
            'anggota_id' => $anggota->id,
            'perubahan' => $perubahan,
            'status' => ProfileEditRequest::STATUS_PENDING,
        ]);

        return $this->successResponse(
            ['siswa' => $this->profileData($anggota), 'permintaan_pending' => $this->pendingCount($anggota)],
            'Perubahan profil terkirim. Data akan berubah setelah disetujui admin.'
        );
    }

    public function edits(Request $request): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);

        if (! $anggota) {
            return $this->errorResponse('Token tidak valid.', 401);
        }

        $permintaan = ProfileEditRequest::where('anggota_id', $anggota->id)
            ->latest()
            ->limit(20)
            ->get(['id', 'perubahan', 'status', 'reviewed_at', 'created_at']);

        return $this->successResponse(
            ['permintaan' => $permintaan, 'permintaan_pending' => $this->pendingCount($anggota)],
            'Daftar permintaan perubahan profil.'
        );
    }

    /**
     * Ganti PIN berlaku langsung, tetapi hanya bila PIN lama benar.
     */
    public function changePin(Request $request): JsonResponse
    {
        $anggota = $this->authenticatedStudent($request);

        if (! $anggota) {
            return $this->errorResponse('Token tidak valid.', 401);
        }

        $data = $request->validate([
            'pin_lama' => ['required', 'string'],
            'pin_baru' => ['required', 'string', 'min:4', 'max:6'],
        ]);

        if (! Hash::check($data['pin_lama'], $anggota->pin ?? '')) {
            return $this->errorResponse('PIN lama tidak sesuai.', 422);
        }

        $anggota->pin = Hash::make($data['pin_baru']);
        $anggota->save();

        return $this->successResponse(['siswa' => $this->profileData($anggota)], 'PIN berhasil diperbarui.');
    }

    private function pendingCount(Anggota $anggota): int
    {
        return ProfileEditRequest::where('anggota_id', $anggota->id)
            ->pending()
            ->count();
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
