<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
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
            'nama_peminjam' => ['required', 'string', 'max:150'],
            'nis' => ['required', 'string', 'max:20'],
            'buku_id' => ['required', 'integer', 'exists:buku,id'],
            'tanggal_ambil' => ['nullable', 'date', 'after_or_equal:today'],
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
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'buku_id.required' => 'Buku wajib dipilih.',
            'buku_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ];
    }
}
