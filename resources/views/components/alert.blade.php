@props(['type' => null, 'message' => null])

@php
    $items = [];

    if ($message) {
        $items[] = ['type' => $type ?? 'success', 'message' => $message];
    }

    if (session('success')) {
        $items[] = ['type' => 'success', 'message' => session('success')];
    }

    if (session('error')) {
        $items[] = ['type' => 'error', 'message' => session('error')];
    }

    if (session('warning')) {
        $items[] = ['type' => 'warning', 'message' => session('warning')];
    }

    $classes = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
    ];
@endphp

@if ($items || $errors->any())
    <div {{ $attributes->merge(['class' => 'space-y-3']) }}>
        @foreach ($items as $item)
            <div class="rounded-md border px-4 py-3 text-sm {{ $classes[$item['type']] ?? $classes['success'] }}">
                {{ $item['message'] }}
            </div>
        @endforeach

        @if ($errors->any())
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-semibold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
