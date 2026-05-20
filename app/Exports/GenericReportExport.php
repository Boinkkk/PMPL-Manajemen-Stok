<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericReportExport implements WithMultipleSheets
{
    /**
     * Buat export laporan baru.
     *
     * @param  array<string, mixed>  $meta
     * @param  array<int, string>  $columns
     * @param  Collection<int, object>  $rows
     * @param  array<string, mixed>  $summary
     */
    public function __construct(
        private readonly array $meta,
        private readonly array $columns,
        private readonly Collection $rows,
        private readonly array $summary,
    ) {}

    /**
     * Daftar sheet export.
     *
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new ReportSummarySheet($this->meta, $this->summary),
            new ReportDataSheet($this->columns, $this->rows),
        ];
    }
}

class ReportSummarySheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    /**
     * Buat sheet ringkasan.
     *
     * @param  array<string, mixed>  $meta
     * @param  array<string, mixed>  $summary
     */
    public function __construct(
        private readonly array $meta,
        private readonly array $summary,
    ) {}

    /**
     * Data ringkasan laporan.
     */
    public function collection(): Collection
    {
        $rows = collect($this->meta)->map(fn (mixed $value, string $key): array => [$key, is_array($value) ? json_encode($value) : $value]);

        foreach ($this->summary as $key => $value) {
            $rows->push([$key, $value]);
        }

        return $rows;
    }

    /**
     * Header sheet ringkasan.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Informasi', 'Nilai'];
    }

    /**
     * Styling sheet.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'F6D78B']]],
        ];
    }
}

class ReportDataSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * Buat sheet data.
     *
     * @param  array<int, string>  $columns
     * @param  Collection<int, object>  $rows
     */
    public function __construct(
        private readonly array $columns,
        private readonly Collection $rows,
    ) {}

    /**
     * Data laporan.
     */
    public function collection(): Collection
    {
        return $this->rows;
    }

    /**
     * Header kolom data.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return collect($this->columns)
            ->map(fn (string $column): string => str($column)->replace('_', ' ')->title()->toString())
            ->all();
    }

    /**
     * Mapping baris.
     *
     * @return array<int, mixed>
     */
    public function map(mixed $row): array
    {
        return collect($this->columns)
            ->map(fn (string $column): mixed => $row->{$column} ?? null)
            ->all();
    }

    /**
     * Styling sheet.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'F6D78B']]],
        ];
    }
}
