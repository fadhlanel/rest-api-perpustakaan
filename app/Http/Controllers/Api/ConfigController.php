<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    public function index(): JsonResponse
    {
        $config = [];
        foreach (Pengaturan::DEFAULTS as $key => $default) {
            $val = Pengaturan::get($key, $default);
            $config[$key] = is_numeric($val) ? (int) $val : $val;
        }

        return $this->successResponse($config, 'Konfigurasi sistem perpustakaan.');
    }
}
