<?php

namespace App\Http\Requests;

use App\Models\ReturProduk;
use Illuminate\Foundation\Http\FormRequest;

class SetujuiReturRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var ReturProduk|null $retur */
            $retur = $this->route('retur');

            if (! $retur instanceof ReturProduk) {
                return;
            }

            if ($retur->jumlah_retur > ($retur->produk?->stok_terkini ?? 0)) {
                $validator->errors()->add('retur', 'Stok produk saat ini tidak cukup untuk menyetujui retur.');
            }

            if ($retur->status !== 'pending') {
                $validator->errors()->add('retur', 'Hanya retur dengan status pending yang bisa disetujui.');
            }
        });
    }
}
