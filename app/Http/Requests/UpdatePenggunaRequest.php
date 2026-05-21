<?php

namespace App\Http\Requests;

use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePenggunaRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna boleh mengubah akun.
     */
    public function authorize(): bool
    {
        return $this->user() instanceof Pengguna && $this->user()->isAdministrator();
    }

    /**
     * Siapkan nilai role/status saat admin mengedit akun sendiri.
     */
    protected function prepareForValidation(): void
    {
        $pengguna = $this->route('pengguna');

        if ($pengguna instanceof Pengguna && (int) $this->user()?->getKey() === (int) $pengguna->getKey()) {
            $this->merge([
                'id_role' => $pengguna->id_role,
                'status' => $pengguna->status,
            ]);
        }
    }

    /**
     * Dapatkan aturan validasi ubah pengguna.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Pengguna $pengguna */
        $pengguna = $this->route('pengguna');

        return [
            'nama_lengkap' => ['required', 'string', 'min:3', 'max:100'],
            'username' => ['required', 'string', 'min:4', 'max:50', 'alpha_num', Rule::unique('pengguna', 'username')->ignore($pengguna->getKey(), 'id_pengguna')],
            'email' => ['required', 'email', 'max:100', Rule::unique('pengguna', 'email')->ignore($pengguna->getKey(), 'id_pengguna')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'required_with:password'],
            'id_role' => ['required', 'integer', 'exists:role,id_role'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
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
            'required_with' => ':attribute wajib diisi saat password baru diisi.',
            'string' => ':attribute harus berupa teks.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
            'email' => ':attribute harus menggunakan format email yang valid.',
            'unique' => ':attribute sudah digunakan.',
            'confirmed' => 'Konfirmasi password tidak sesuai.',
            'exists' => ':attribute tidak ditemukan.',
            'in' => ':attribute tidak valid.',
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
            'nama_lengkap' => 'nama lengkap',
            'username' => 'username',
            'email' => 'email',
            'password' => 'password',
            'password_confirmation' => 'konfirmasi password',
            'id_role' => 'role',
            'status' => 'status',
        ];
    }
}
