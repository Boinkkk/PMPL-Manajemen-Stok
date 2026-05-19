<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ApproveOrderDistribusiRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh menyetujui order.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageStock() === true;
    }

    /**
     * Aturan validasi persetujuan order.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'details' => ['required', 'array', 'min:1'],
            'details.*.id_detail_order' => ['required', 'integer', 'exists:detail_order,id_detail_order'],
            'details.*.jumlah_disetujui' => ['required', 'integer', 'min:0'],
            'catatan_persetujuan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Validasi setidaknya satu item disetujui.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $totalDisetujui = collect($this->input('details', []))
                    ->sum(fn (array $detail): int => (int) ($detail['jumlah_disetujui'] ?? 0));

                if ($totalDisetujui < 1) {
                    $validator->errors()->add('details', 'Minimal satu produk harus memiliki jumlah disetujui.');
                }
            },
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
            'details' => 'detail persetujuan',
            'details.*.id_detail_order' => 'detail order',
            'details.*.jumlah_disetujui' => 'jumlah disetujui',
            'catatan_persetujuan' => 'catatan persetujuan',
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
            'exists' => ':attribute tidak valid.',
            'array' => ':attribute harus berupa daftar data.',
            'min' => ':attribute minimal :min.',
            'integer' => ':attribute harus berupa angka bulat.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }
}
