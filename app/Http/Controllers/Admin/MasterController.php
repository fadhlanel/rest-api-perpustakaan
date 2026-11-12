<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index(string $type)
    {
        abort_unless(in_array($type, ['kategori', 'penulis', 'penerbit'], true), 404);
        $model = ['kategori' => Kategori::class, 'penulis' => Penulis::class, 'penerbit' => Penerbit::class][$type];

        return view('admin.master.index', ['type' => $type, 'items' => $model::latest()->paginate(15)]);
    }

    public function store(Request $request, string $type)
    {
        $model = $this->model($type);
        $tableName = (new $model)->getTable();
        $data = $request->validate(['nama' => 'required|string|max:100|unique:'.$tableName.',nama']);
        $item = $model::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($type).' berhasil ditambah.',
                'data' => [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'type' => $type,
                ],
            ], 201);
        }

        return back()->with('success', ucfirst($type).' berhasil ditambah.');
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = $this->model($type);
        $tableName = (new $model)->getTable();
        $item = $model::findOrFail($id);
        $item->update($request->validate(['nama' => 'required|string|max:100|unique:'.$tableName.',nama,'.$id]));

        return back()->with('success', ucfirst($type).' berhasil diupdate.');
    }

    public function destroy(string $type, int $id)
    {
        $model = $this->model($type);
        $item = $model::findOrFail($id);
        abort_if($item->bukus()->exists(), 422, ucfirst($type).' masih digunakan oleh buku.');
        $item->delete();

        return back()->with('success', ucfirst($type).' berhasil dihapus.');
    }

    private function model(string $type): string
    {
        return ['kategori' => Kategori::class, 'penulis' => Penulis::class, 'penerbit' => Penerbit::class][$type] ?? abort(404);
    }
}
