<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-md border border-jamu-border bg-jamu-surface shadow-sm']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-jamu-border text-sm">
            {{ $slot }}
        </table>
    </div>
</div>
