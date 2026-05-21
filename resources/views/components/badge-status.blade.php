@props(['status'])

@php
    $classes = [
        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'disetujui' => 'bg-green-100 text-green-800 border-green-200',
        'ditolak' => 'bg-red-100 text-red-800 border-red-200',
        'selesai' => 'bg-jamu-secondary-light text-jamu-primary-dark border-jamu-secondary',
        'belum_dibaca' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'dibaca' => 'bg-green-100 text-green-800 border-green-200',
    ][$status] ?? 'bg-jamu-surface text-jamu-muted border-jamu-border';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md border px-2 py-1 text-xs font-medium {$classes}"]) }}>
    {{ str_replace('_', ' ', ucfirst((string) $status)) }}
</span>
