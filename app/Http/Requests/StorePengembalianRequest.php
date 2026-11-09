<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengembalianRequest extends FormRequest
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
            'peminjaman_id' => ['required', 'integer', 'exists:peminjaman,id'],
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
            'peminjaman_id.required' => 'Peminjaman wajib dipilih.',
            'peminjaman_id.exists' => 'Data peminjaman tidak ditemukan.',
        ];
    }
}
