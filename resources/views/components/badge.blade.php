@props(['status'])

@php
    $normalized = (string) $status;
    $classes = [
        'aktif' => 'border-green-200 bg-green-50 text-green-800',
        'nonaktif' => 'border-red-200 bg-red-50 text-red-800',
        'Administrator' => 'border-jamu-secondary bg-jamu-secondary-light text-jamu-primary-dark',
        'Staf Gudang' => 'border-jamu-green bg-green-50 text-jamu-green',
        'Manajer' => 'border-jamu-border bg-jamu-bg text-jamu-muted',
        'pending' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
        'disetujui' => 'border-blue-200 bg-blue-50 text-blue-800',
        'ditolak' => 'border-red-200 bg-red-50 text-red-800',
        'selesai' => 'border-green-200 bg-green-50 text-green-800',
    ][$normalized] ?? 'border-jamu-border bg-jamu-surface text-jamu-muted';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md border px-2 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ str_replace('_', ' ', ucfirst($normalized)) }}
</span>
