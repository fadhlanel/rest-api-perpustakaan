<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
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

        $query = $model::query();

        if (request()->filled('search')) {
            $search = request()->string('search')->toString();
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $items = $query->latest()->paginate(10)->onEachSide(0)->withQueryString();

        return view('admin.master.index', ['type' => $type, 'items' => $items]);
    }

    public function create(string $type)
    {
        abort_unless(in_array($type, ['kategori', 'penulis', 'penerbit'], true), 404);

        return view('admin.master.create', ['type' => $type]);
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

        $field = $type.'_id';
        $bukuTerdampak = Buku::where($field, $id)->count();

        $item->delete();

        if ($bukuTerdampak > 0) {
            return redirect()
                ->route('admin.master.index', $type)
                ->with('warning', "{$bukuTerdampak} Buku data {$type}nya kosong, perbaiki datanya segera!")
                ->with('warning_url', route('admin.master.reassign', $type));
        }

        return back()->with('success', ucfirst($type).' berhasil dihapus.');
    }

    public function reassign(string $type)
    {
        abort_unless(in_array($type, ['kategori', 'penulis', 'penerbit'], true), 404);

        $field = $type.'_id';
        $bukus = Buku::whereNull($field)->with(['kategori', 'penulis', 'penerbit'])->paginate(10)->onEachSide(0)->withQueryString();

        $model = $this->model($type);
        $options = $model::orderBy('nama')->get();

        return view('admin.master.reassign', compact('type', 'bukus', 'options', 'field'));
    }

    public function reassignBulk(Request $request, string $type)
    {
        $request->validate(['new_id' => 'required|integer|exists:'.$type.',id']);

        $field = $type.'_id';
        $count = Buku::whereNull($field)->update([$field => $request->new_id]);

        return redirect()
            ->route('admin.master.index', $type)
            ->with('success', "{$count} buku berhasil dipindahkan ke {$type} baru.");
    }

    public function reassignItem(Request $request, string $type)
    {
        $request->validate([
            'buku_id' => 'required|integer|exists:buku,id',
            'new_id' => 'required|integer|exists:'.$type.',id',
        ]);

        $field = $type.'_id';
        Buku::where('id', $request->buku_id)->update([$field => $request->new_id]);

        return back()->with('success', 'Buku berhasil dipindahkan.');
    }

    private function model(string $type): string
    {
        return ['kategori' => Kategori::class, 'penulis' => Penulis::class, 'penerbit' => Penerbit::class][$type] ?? abort(404);
    }
}
