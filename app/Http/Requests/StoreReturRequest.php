<?php

namespace App\Http\Requests;

use App\Models\Produk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_distributor' => ['required', 'integer', Rule::exists('distributor', 'id_distributor')],
            'id_produk' => ['required', 'integer', Rule::exists('produk', 'id_produk')],
            'jumlah_retur' => ['required', 'integer', 'min:1'],
            'alasan' => ['required', 'string'],
            'foto_bukti' => ['nullable', 'image', 'max:2048'],
            'id_supplier' => ['nullable', 'integer', Rule::exists('supplier', 'id_supplier')],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('id_produk') || ! $this->filled('jumlah_retur')) {
                return;
            }

            $produk = Produk::find($this->id_produk);

            if (! $produk) {
                return;
            }

            if ($this->jumlah_retur > $produk->stok_terkini) {
                $validator->errors()->add('jumlah_retur', 'Jumlah retur tidak boleh melebihi stok terkini produk.');
            }
        });
    }
}
