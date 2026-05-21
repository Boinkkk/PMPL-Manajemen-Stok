<?php

namespace App\Http\Requests;

use App\Models\Batch;
use App\Models\Pengguna;
use App\Models\Produk;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class StoreStokKeluarRequest extends FormRequest
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
            'id_distributor' => ['required', 'integer', 'exists:distributor,id_distributor'],
            'id_order' => ['nullable', 'integer', 'exists:order_distribusi,id_order'],
            'tanggal_keluar' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.id_produk' => ['required', 'integer', 'exists:produk,id_produk'],
            'details.*.id_batch' => ['required', 'integer', 'exists:batch,id_batch'],
            'details.*.jumlah' => ['required', 'integer', 'min:1'],
            'details.*.harga_jual' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Tambahkan validasi stok dan batch setelah aturan dasar lolos.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $details = collect($this->input('details', []));

                $this->validateProductStock($validator, $details);
                $this->validateBatchOwnership($validator, $details);
            },
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
            'id_distributor' => 'distributor',
            'id_order' => 'order distribusi',
            'tanggal_keluar' => 'tanggal keluar',
            'catatan' => 'catatan',
            'details' => 'detail produk',
            'details.*.id_produk' => 'produk',
            'details.*.id_batch' => 'batch',
            'details.*.jumlah' => 'jumlah',
            'details.*.harga_jual' => 'harga jual',
        ];
    }

    /**
     * Validasi total jumlah keluar tidak melebihi stok produk.
     *
     * @param  Collection<int, array<string, mixed>>  $details
     */
    private function validateProductStock(Validator $validator, Collection $details): void
    {
        $jumlahPerProduk = $details
            ->groupBy('id_produk')
            ->map(fn (Collection $rows): int => (int) $rows->sum('jumlah'));

        $produkById = Produk::query()
            ->whereIn('id_produk', $jumlahPerProduk->keys())
            ->get()
            ->keyBy('id_produk');

        foreach ($jumlahPerProduk as $idProduk => $jumlah) {
            $produk = $produkById->get((int) $idProduk);

            if ($produk !== null && $jumlah > $produk->stok_terkini) {
                $validator->errors()->add(
                    'details',
                    "Stok produk {$produk->nama_produk} tidak mencukupi. Stok tersedia {$produk->stok_terkini}, diminta {$jumlah}."
                );
            }
        }
    }

    /**
     * Validasi batch sesuai produk dan belum kedaluwarsa.
     *
     * @param  Collection<int, array<string, mixed>>  $details
     */
    private function validateBatchOwnership(Validator $validator, Collection $details): void
    {
        $batchById = Batch::query()
            ->whereIn('id_batch', $details->pluck('id_batch')->filter()->unique())
            ->get()
            ->keyBy('id_batch');

        foreach ($details as $index => $detail) {
            $batch = $batchById->get((int) ($detail['id_batch'] ?? 0));

            if ($batch === null) {
                continue;
            }

            if ((int) $batch->id_produk !== (int) $detail['id_produk']) {
                $validator->errors()->add("details.{$index}.id_batch", 'Batch yang dipilih tidak sesuai dengan produk.');
            }

            if ($batch->tanggal_expired->lt(today())) {
                $validator->errors()->add("details.{$index}.id_batch", 'Batch yang dipilih sudah kedaluwarsa.');
            }
        }
    }
}
