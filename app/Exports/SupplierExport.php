<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierExport implements WithMultipleSheets
{
    /**
     * Buat export data supplier.
     *
     * @param  Collection<int, mixed>  $rows
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        private readonly Collection $rows,
        private readonly array $summary,
        private readonly array $filters,
    ) {}

    /**
     * Sheet export.
     *
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new SupplierSummarySheet($this->summary, $this->filters, $this->rows->count()),
            new SupplierDataSheet($this->rows),
        ];
    }
}

class SupplierSummarySheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    /**
     * Buat sheet ringkasan.
     *
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        private readonly array $summary,
        private readonly array $filters,
        private readonly int $jumlahData,
    ) {}

    /**
     * Data ringkasan.
     */
    public function collection(): Collection
    {
        return collect([
            ['Judul', 'Data Supplier'],
            ['Waktu Export', now('Asia/Jakarta')->translatedFormat('d F Y H:i')],
            ['Pencarian', $this->filters['q'] ?? '-'],
            ['Urutan', $this->filters['sort'] ?? 'nama_asc'],
            ['Jumlah Data Diekspor', $this->jumlahData],
            ['Total Supplier Terdaftar', $this->summary['total_supplier'] ?? 0],
            ['Supplier Aktif', $this->summary['supplier_aktif'] ?? 0],
            ['Supplier Tidak Aktif', $this->summary['supplier_tidak_aktif'] ?? 0],
            ['Total Nilai Pasokan Bulan Ini', (float) ($this->summary['total_nilai_bulan_ini'] ?? 0)],
        ]);
    }

    /**
     * Header sheet.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Item', 'Nilai'];
    }

    /**
     * Judul sheet.
     */
    public function title(): string
    {
        return 'Ringkasan';
    }

    /**
     * Style sheet.
     */
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()->setFillType('solid')->getStartColor()->setRGB('F6D78B');

        return [];
    }
}

class SupplierDataSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * Buat sheet data.
     *
     * @param  Collection<int, mixed>  $rows
     */
    public function __construct(
        private readonly Collection $rows,
    ) {}

    /**
     * Data supplier.
     */
    public function collection(): Collection
    {
        return $this->rows;
    }

    /**
     * Header kolom.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Kode Supplier',
            'Nama Supplier',
            'Alamat',
            'Telepon',
            'Email',
            'Kontak Person',
            'Total Transaksi',
            'Total Nilai Pasokan',
            'Tanggal Ditambahkan',
        ];
    }

    /**
     * Mapping baris.
     *
     * @param  mixed  $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        return [
            $row->kode_supplier,
            $row->nama_supplier,
            $row->alamat,
            $row->telepon,
            $row->email,
            $row->kontak_person,
            (int) $row->total_transaksi,
            (float) $row->total_nilai_pasokan,
            $row->created_at?->locale('id')->translatedFormat('d F Y H:i'),
        ];
    }

    /**
     * Judul sheet.
     */
    public function title(): string
    {
        return 'Data Supplier';
    }

    /**
     * Style sheet.
     */
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()->setFillType('solid')->getStartColor()->setRGB('F6D78B');

        return [];
    }
}
