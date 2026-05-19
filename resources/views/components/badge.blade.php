@props(['status'])

@php
    $normalized = (string) $status;
    $classes = [
        'aktif' => 'border-green-200 bg-green-50 text-green-800',
        'nonaktif' => 'border-red-200 bg-red-50 text-red-800',
        'Administrator' => 'border-jamu-secondary bg-jamu-secondary-light text-jamu-primary-dark',
        'Staf Gudang' => 'border-jamu-green bg-green-50 text-jamu-green',
        'Manajer' => 'border-jamu-border bg-jamu-bg text-jamu-muted',
    ][$normalized] ?? 'border-jamu-border bg-jamu-surface text-jamu-muted';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md border px-2 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ str_replace('_', ' ', ucfirst($normalized)) }}
</span>
