<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh mengubah supplier.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Administrator', 'Staf Gudang']) === true;
    }

    /**
     * Aturan validasi ubah supplier.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nama_supplier' => ['required', 'string', 'min:3', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'telepon' => ['nullable', 'string', 'min:8', 'max:20', 'regex:/^[0-9+()\\s-]+$/'],
            'email' => ['nullable', 'email', 'max:100'],
            'kontak_person' => ['nullable', 'string', 'min:3', 'max:100'],
        ];
    }

    /**
     * Validasi duplikasi case-insensitive dengan pengecualian data sendiri.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $supplier = $this->route('supplier');
                $idSupplier = $supplier instanceof Supplier ? $supplier->id_supplier : (int) $supplier;
                $namaSupplier = (string) $this->input('nama_supplier');
                $email = $this->input('email');

                if ($namaSupplier !== '' && DB::table('supplier')
                    ->where('id_supplier', '!=', $idSupplier)
                    ->whereRaw('LOWER(nama_supplier) = ?', [mb_strtolower($namaSupplier)])
                    ->exists()) {
                    $validator->errors()->add('nama_supplier', 'Nama supplier sudah terdaftar.');
                }

                if ($email && DB::table('supplier')
                    ->where('id_supplier', '!=', $idSupplier)
                    ->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $email)])
                    ->exists()) {
                    $validator->errors()->add('email', 'Email supplier sudah digunakan.');
                }
            },
        ];
    }

    /**
     * Pesan validasi Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_supplier.min' => 'Nama supplier minimal 3 karakter.',
            'nama_supplier.max' => 'Nama supplier maksimal 150 karakter.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'telepon.min' => 'Telepon minimal 8 karakter.',
            'telepon.max' => 'Telepon maksimal 20 karakter.',
            'telepon.regex' => 'Telepon hanya boleh berisi angka, +, spasi, tanda hubung, dan tanda kurung.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'kontak_person.min' => 'Kontak person minimal 3 karakter.',
            'kontak_person.max' => 'Kontak person maksimal 100 karakter.',
        ];
    }
}
