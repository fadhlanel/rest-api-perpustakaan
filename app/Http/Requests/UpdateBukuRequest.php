<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuRequest extends FormRequest
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
        // Semua field opsional (sometimes) karena update boleh parsial.
        return [
            'judul' => ['sometimes', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tahun_terbit' => ['sometimes', 'integer', 'min:1900', 'max:'.now()->year],
            'isbn' => ['nullable', 'string', 'max:30'],
            'sampul' => ['nullable', 'string', 'max:255'],
            'stok' => ['sometimes', 'integer', 'min:1', 'max:10000'],
            'kategori_id' => ['sometimes', 'integer', 'exists:kategori,id'],
            'penulis_id' => ['sometimes', 'integer', 'exists:penulis,id'],
            'penerbit_id' => ['sometimes', 'integer', 'exists:penerbit,id'],
        ];
    }
}
