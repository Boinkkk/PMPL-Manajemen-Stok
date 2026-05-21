<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrderDistribusiRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh membuat order.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageStock() === true;
    }

    /**
     * Aturan validasi pembuatan order distribusi.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id_distributor' => ['required', 'integer', 'exists:distributor,id_distributor'],
            'tanggal_order' => ['required', 'date', 'before_or_equal:today'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.id_produk' => ['required', 'integer', 'exists:produk,id_produk'],
            'details.*.jumlah_diminta' => ['required', 'integer', 'min:1'],
            'details.*.harga_satuan' => ['required', 'numeric', 'gt:0'],
            'details.*.catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Tambahkan validasi produk duplikat.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $produkIds = collect($this->input('details', []))
                    ->pluck('id_produk')
                    ->filter()
                    ->map(fn (mixed $idProduk): int => (int) $idProduk);

                if ($produkIds->count() !== $produkIds->unique()->count()) {
                    $validator->errors()->add('details', 'Produk yang sama tidak boleh dipilih lebih dari satu baris.');
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
            'id_distributor' => 'distributor',
            'tanggal_order' => 'tanggal order',
            'catatan' => 'catatan',
            'details' => 'detail produk',
            'details.*.id_produk' => 'produk',
            'details.*.jumlah_diminta' => 'jumlah diminta',
            'details.*.harga_satuan' => 'harga satuan',
            'details.*.catatan' => 'catatan baris',
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
            'date' => ':attribute harus berupa tanggal yang valid.',
            'before_or_equal' => ':attribute tidak boleh melewati hari ini.',
            'array' => ':attribute harus berupa daftar data.',
            'min' => ':attribute minimal :min.',
            'integer' => ':attribute harus berupa angka bulat.',
            'numeric' => ':attribute harus berupa angka.',
            'gt' => ':attribute harus lebih dari 0.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }
}
