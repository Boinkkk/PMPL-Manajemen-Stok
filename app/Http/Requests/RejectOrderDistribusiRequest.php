<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectOrderDistribusiRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh menolak order.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageStock() === true;
    }

    /**
     * Aturan validasi penolakan order.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    /**
     * Nama atribut validasi Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'alasan' => 'alasan penolakan',
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
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }
}
