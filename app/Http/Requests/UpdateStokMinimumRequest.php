<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStokMinimumRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh mengubah stok minimum.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdministrator() === true;
    }

    /**
     * Aturan validasi stok minimum.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'stok_minimum' => ['required', 'array'],
            'stok_minimum.*' => ['required', 'integer', 'min:0', 'max:10000'],
        ];
    }

    /**
     * Pesan error validasi Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'stok_minimum.required' => 'Data stok minimum wajib diisi.',
            'stok_minimum.*.required' => 'Stok minimum wajib diisi.',
            'stok_minimum.*.integer' => 'Stok minimum harus berupa angka bulat.',
            'stok_minimum.*.min' => 'Stok minimum tidak boleh negatif.',
            'stok_minimum.*.max' => 'Stok minimum maksimal 10.000.',
        ];
    }
}
