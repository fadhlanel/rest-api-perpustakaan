# Pemetaan Endpoint API

Sumber kebenaran: `routes/api.php`, `routes/web.php`, dan pemakaian di aplikasi Flutter (`Anodor/lib`).

Base URL development: `http://10.0.2.2:8000/api/v1` (emulator) atau `http://IP-LAN:8000/api/v1` (HP fisik).
Base URL production: `https://APP-NAME.back4.app/api/v1` (contoh: `https://restapiperpustakaan2-kpgkjk3w.back4.app/api/v1`).

Semua endpoint API memakai prefix `/api/v1`. Tidak ada autentikasi: seluruh endpoint publik.

## Envelope respons

Sukses:

```json
{
  "success": true,
  "message": "Daftar buku perpustakaan.",
  "data": [],
  "meta": { "current_page": 1, "per_page": 10, "total": 0, "last_page": 1 }
}
```

Gagal:

```json
{
  "success": false,
  "message": "Data tidak ditemukan."
}
```

Validasi (422) mengikuti format Laravel: `message` + `errors{field: [pesan]}`.

## Endpoint yang dipakai Flutter

| Flutter | Method | Path | Query/Body | Respons dipakai |
|---|---|---|---|---|
| `BukuProvider.loadFirst` | GET | `/buku` | `per_page=20`, `search`, `kategori_id` | `data[]`, `meta.current_page`, `meta.last_page`, `meta.total` |
| `BukuProvider.loadMore` | GET | `/buku` | `per_page=20`, `page`, `search`, `kategori_id` | idem |
| `BukuProvider.fetchDetail` | GET | `/buku/{id}` | — | `data` objek buku |
| `BukuProvider.loadKategoris` | GET | `/kategori` | `per_page=50`, `page=1` | `data[]` |
| `PinjamanProvider.fetchApiLoans` | GET | `/peminjaman` | — | `data[]` |
| `PinjamanProvider.pinjamBuku` | POST | `/peminjaman` | `buku_id`, `nis`, `nama_peminjam`, `tanggal_ambil` | status + `data` |
| `PinjamanProvider.perpanjangPinjaman` | POST | `/peminjaman/{id}/perpanjang` | — | `data` peminjaman terbaru |
| `NetworkService.checkConnection` | GET | `/api` | — | `status`, `database` |

`tanggal_ambil` nullable; backend mengisi `now()` bila kosong.

## Endpoint backend yang belum dipakai Flutter

| Method | Path | Keterangan |
|---|---|---|
| GET | `/kategori/{id}` | detail kategori |
| POST/PUT/DELETE | `/kategori`, `/kategori/{id}` | CRUD kategori |
| GET/POST/PUT/DELETE | `/penulis`, `/penulis/{id}` | CRUD penulis |
| GET/POST/PUT/DELETE | `/penerbit`, `/penerbit/{id}` | CRUD penerbit |
| POST/PUT/DELETE | `/buku` | tambah/ubah/hapus buku |
| GET | `/peminjaman/{id}` | detail peminjaman |
| GET | `/pengembalian` | daftar pengembalian |
| POST | `/pengembalian` | catat pengembalian |

Semua operasi tulis dapat dipanggil tanpa autentikasi. Ini keterbatasan yang diketahui, bukan bug: proyek belum memiliki Sanctum/role.

## Kode status

| Kode | Kondisi | Penanganan Flutter |
|---|---|---|
| 200 | sukses | tampilkan data |
| 201 | data dibuat | tampilkan data baru |
| 400 | aturan bisnis gagal (stok habis, stok tidak boleh minus) | tampilkan `message` |
| 404 | data tidak ada | tampilkan `message` |
| 422 | validasi gagal | tampilkan `message`/`errors` |
| 429 | rate limit | tampilkan pesan coba lagi |
| 500 | error server | tampilkan pesan umum |
| — | koneksi gagal/timeout | fallback cache offline |

## Health

`GET /api` → `200 {"status":"ok","database":"ok"}` atau `503 {"status":"error","database":"unavailable"}`.

`GET /up` → health bawaan Laravel (tanpa cek database).
