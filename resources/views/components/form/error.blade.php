@props(['name'])

@error($name)
    <p {{ $attributes->merge(['class' => 'text-sm text-red-700']) }}>{{ $message }}</p>
@enderror
