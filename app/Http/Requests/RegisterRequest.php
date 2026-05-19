<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Izinkan register publik khusus kebutuhan testing.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi register testing.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'min:3', 'max:100'],
            'username' => ['required', 'string', 'min:4', 'max:50', 'alpha_num', Rule::unique('pengguna', 'username')],
            'email' => ['required', 'email', 'max:100', Rule::unique('pengguna', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'id_role' => ['required', 'integer', 'exists:role,id_role'],
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
            'string' => ':attribute harus berupa teks.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
            'email' => ':attribute harus menggunakan format email yang valid.',
            'unique' => ':attribute sudah digunakan.',
            'confirmed' => 'Konfirmasi password tidak sesuai.',
            'exists' => ':attribute tidak ditemukan.',
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
        ];
    }
}
