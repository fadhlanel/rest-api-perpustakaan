<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tahun_terbit' => ['required', 'integer', 'min:1900', 'max:'.now()->year],
            'isbn' => ['nullable', 'string', 'max:30'],
            'sampul' => ['nullable', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:1', 'max:10000'],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'penulis_id' => ['required', 'integer', 'exists:penulis,id'],
            'penerbit_id' => ['required', 'integer', 'exists:penerbit,id'],
        ];
    }

    /**
     * Pesan validasi kustom (bahasa Indonesia).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul buku wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.min' => 'Tahun terbit minimal 1900.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi tahun sekarang.',
            'stok.required' => 'Stok buku wajib diisi.',
            'stok.min' => 'Stok buku minimal 1.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'penulis_id.required' => 'Penulis wajib dipilih.',
            'penulis_id.exists' => 'Penulis yang dipilih tidak ditemukan.',
            'penerbit_id.required' => 'Penerbit wajib dipilih.',
            'penerbit_id.exists' => 'Penerbit yang dipilih tidak ditemukan.',
        ];
    }
}
