<?php

namespace App\Exports;

use App\Models\OrderDistribusi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderDistribusiExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * Buat export order distribusi baru.
     *
     * @param  Collection<int, OrderDistribusi>  $orders
     */
    public function __construct(private readonly Collection $orders) {}

    /**
     * Ambil collection data untuk export.
     *
     * @return Collection<int, OrderDistribusi>
     */
    public function collection(): Collection
    {
        return $this->orders;
    }

    /**
     * Header kolom export.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Nomor Order',
            'Distributor',
            'Tanggal Order',
            'Tanggal Diproses',
            'Total Item',
            'Total Nilai',
            'Status',
            'Dibuat Oleh',
        ];
    }

    /**
     * Mapping baris order ke kolom export.
     *
     * @return array<int, mixed>
     */
    public function map(mixed $row): array
    {
        return [
            $row->nomor_order,
            $row->distributor?->nama_distributor ?? '-',
            $row->tanggal_order?->format('Y-m-d'),
            $row->tanggal_diproses?->format('Y-m-d') ?? '-',
            $row->detail_orders_count,
            (float) ($row->total_nilai_order ?? 0),
            $row->status,
            $row->pengguna?->nama_lengkap ?? '-',
        ];
    }
}
