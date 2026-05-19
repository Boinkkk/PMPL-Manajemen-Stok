<?php

namespace App\Http\Requests;

use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStokMasukRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $pengguna = $this->user();

        return $pengguna instanceof Pengguna && $pengguna->canManageStock();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_supplier' => ['required', 'integer', 'exists:supplier,id_supplier'],
            'tanggal_masuk' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.id_produk' => ['required', 'integer', 'exists:produk,id_produk'],
            'details.*.nomor_batch' => ['required', 'string', 'max:50'],
            'details.*.tanggal_produksi' => ['nullable', 'date'],
            'details.*.tanggal_expired' => ['required', 'date', 'after_or_equal:tanggal_masuk'],
            'details.*.jumlah' => ['required', 'integer', 'min:1'],
            'details.*.harga_beli' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Dapatkan pesan error validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'numeric' => ':attribute harus berupa angka.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'exists' => ':attribute tidak ditemukan.',
            'array' => ':attribute harus berupa daftar data.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max karakter.',
            'after_or_equal' => ':attribute tidak boleh lebih awal dari tanggal masuk.',
        ];
    }

    /**
     * Dapatkan nama atribut validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id_supplier' => 'supplier',
            'tanggal_masuk' => 'tanggal masuk',
            'catatan' => 'catatan',
            'details' => 'detail produk',
            'details.*.id_produk' => 'produk',
            'details.*.nomor_batch' => 'nomor batch',
            'details.*.tanggal_produksi' => 'tanggal produksi',
            'details.*.tanggal_expired' => 'tanggal kedaluwarsa',
            'details.*.jumlah' => 'jumlah',
            'details.*.harga_beli' => 'harga beli',
        ];
    }
}
