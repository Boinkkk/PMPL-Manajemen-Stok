<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDataEoqRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh mengubah data EOQ.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageStock() === true;
    }

    /**
     * Aturan validasi data EOQ.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'permintaan_tahunan' => ['required', 'integer', 'min:0', 'max:999999999'],
            'biaya_pemesanan' => ['required', 'integer', 'min:1', 'max:999999999'],
            'biaya_penyimpanan' => ['required', 'integer', 'min:1', 'max:999999999'],
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
            'permintaan_tahunan.required' => 'Permintaan tahunan wajib diisi.',
            'permintaan_tahunan.integer' => 'Permintaan tahunan harus berupa angka bulat.',
            'permintaan_tahunan.min' => 'Permintaan tahunan tidak boleh negatif.',
            'biaya_pemesanan.required' => 'Biaya pemesanan wajib diisi.',
            'biaya_pemesanan.integer' => 'Biaya pemesanan harus berupa angka bulat.',
            'biaya_pemesanan.min' => 'Biaya pemesanan minimal 1.',
            'biaya_penyimpanan.required' => 'Biaya penyimpanan wajib diisi.',
            'biaya_penyimpanan.integer' => 'Biaya penyimpanan harus berupa angka bulat.',
            'biaya_penyimpanan.min' => 'Biaya penyimpanan minimal 1.',
        ];
    }
}
