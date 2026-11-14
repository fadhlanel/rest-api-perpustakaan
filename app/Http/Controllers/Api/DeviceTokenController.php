<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:255',
            'platform' => 'in:android,ios',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = auth('sanctum')->id();
        $platform = $request->input('platform', 'android');

        DeviceToken::registerToken($userId, $request->token, $platform);

        return response()->json([
            'success' => true,
            'message' => 'Device token berhasil didaftarkan',
        ]);
    }

    public function unregister(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deleted = DeviceToken::unregisterToken($request->token);

        return response()->json([
            'success' => true,
            'message' => $deleted ? 'Device token berhasil dihapus' : 'Token tidak ditemukan',
        ]);
    }
}
