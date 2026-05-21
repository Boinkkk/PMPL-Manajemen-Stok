@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
])

<label class="flex flex-col gap-1 text-sm">
    <span class="font-medium text-jamu-text">{{ $label }}</span>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        @required($required)
        {{ $attributes->merge(['class' => 'rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary']) }}
    >
    <x-form.error :name="$name" />
</label>
