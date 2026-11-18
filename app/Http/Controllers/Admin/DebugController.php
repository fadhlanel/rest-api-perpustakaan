<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DebugController extends Controller
{
    public function check()
    {
        $checks = [];

        // 1. GD Extension
        try {
            $manager = new ImageManager(new Driver);
            $checks['gd_extension'] = 'OK - ImageManager initialized';
        } catch (\Throwable $e) {
            $checks['gd_extension'] = 'ERROR: '.$e->getMessage();
        }

        // 2. Migration status - check if kategori_id is nullable
        try {
            $nullable = DB::select("SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'buku' AND COLUMN_NAME = 'kategori_id'");
            $checks['kategori_id_nullable'] = $nullable[0]->IS_NULLABLE ?? 'UNKNOWN';
        } catch (\Throwable $e) {
            $checks['kategori_id_nullable'] = 'ERROR: '.$e->getMessage();
        }

        // 3. Can query null books
        try {
            $count = Buku::whereNull('kategori_id')->count();
            $checks['query_null_kategori'] = "OK - {$count} books with null kategori_id";
        } catch (\Throwable $e) {
            $checks['query_null_kategori'] = 'ERROR: '.$e->getMessage();
        }

        // 4. Doctrine DBAL installed
        $checks['doctrine_dbal'] = class_exists('Doctrine\DBAL\Connection') ? 'OK' : 'NOT INSTALLED';

        // 5. PHP version
        $checks['php_version'] = PHP_VERSION;

        // 6. Loaded extensions
        $checks['loaded_extensions'] = implode(', ', ['gd' => extension_loaded('gd') ? 'YES' : 'NO']);

        // 7. Test BukuController index logic
        try {
            $query = Buku::with(['kategori', 'penulis', 'penerbit']);
            $items = $query->latest()->paginate(10)->onEachSide(0);
            $checks['buku_index_query'] = 'OK - '.$items->total().' books loaded';
        } catch (\Throwable $e) {
            $checks['buku_index_query'] = 'ERROR: '.$e->getMessage().' | '.$e->getFile().':'.$e->getLine();
        }

        // 8. Test reassign view data
        try {
            $bukus = Buku::whereNull('kategori_id')->with(['kategori', 'penulis', 'penerbit'])->paginate(10)->onEachSide(0);
            $checks['reassign_query'] = 'OK - '.$bukus->total().' orphan books';
        } catch (\Throwable $e) {
            $checks['reassign_query'] = 'ERROR: '.$e->getMessage().' | '.$e->getFile().':'.$e->getLine();
        }

        // 9. Test BukuController view render
        try {
            $query = Buku::with(['kategori', 'penulis', 'penerbit']);
            $bukus = $query->latest()->paginate(10)->onEachSide(0)->withQueryString();
            $view = view('admin.buku.index', compact('bukus'))->render();
            $checks['buku_view_render'] = 'OK - View rendered ('.strlen($view).' bytes)';
        } catch (\Throwable $e) {
            $checks['buku_view_render'] = 'ERROR: '.$e->getMessage().' | '.$e->getFile().':'.$e->getLine();
        }

        // 10. Test reassign view render
        try {
            $type = 'kategori';
            $bukus = Buku::whereNull('kategori_id')->with(['kategori', 'penulis', 'penerbit'])->paginate(10)->onEachSide(0)->withQueryString();
            $options = Kategori::orderBy('nama')->get();
            $field = 'kategori_id';
            $view = view('admin.master.reassign', compact('type', 'bukus', 'options', 'field'))->render();
            $checks['reassign_view_render'] = 'OK - View rendered ('.strlen($view).' bytes)';
        } catch (\Throwable $e) {
            $checks['reassign_view_render'] = 'ERROR: '.$e->getMessage().' | '.$e->getFile().':'.$e->getLine();
        }

        return response()->json($checks, 200, [], JSON_PRETTY_PRINT);
    }
}
