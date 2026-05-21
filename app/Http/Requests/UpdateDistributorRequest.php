<?php

namespace App\Http\Requests;

use App\Models\Distributor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class UpdateDistributorRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh mengubah distributor.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Administrator', 'Staf Gudang']) === true;
    }

    /**
     * Aturan validasi ubah distributor.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nama_distributor' => ['required', 'string', 'min:3', 'max:150'],
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
                $distributor = $this->route('distributor');
                $idDistributor = $distributor instanceof Distributor ? $distributor->id_distributor : (int) $distributor;
                $namaDistributor = (string) $this->input('nama_distributor');
                $email = $this->input('email');

                if ($namaDistributor !== '' && DB::table('distributor')
                    ->where('id_distributor', '!=', $idDistributor)
                    ->whereRaw('LOWER(nama_distributor) = ?', [mb_strtolower($namaDistributor)])
                    ->exists()) {
                    $validator->errors()->add('nama_distributor', 'Nama distributor sudah terdaftar.');
                }

                if ($email && DB::table('distributor')
                    ->where('id_distributor', '!=', $idDistributor)
                    ->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $email)])
                    ->exists()) {
                    $validator->errors()->add('email', 'Email distributor sudah digunakan.');
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
            'nama_distributor.required' => 'Nama distributor wajib diisi.',
            'nama_distributor.min' => 'Nama distributor minimal 3 karakter.',
            'nama_distributor.max' => 'Nama distributor maksimal 150 karakter.',
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
