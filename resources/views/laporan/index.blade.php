<x-layouts.app title="Laporan">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-semibold">Pusat Laporan</h2>
            <p class="text-sm text-jamu-muted">Pilih laporan stok, distribusi, atau supplier.</p>
        </div>

        @foreach ($groups as $group => $reports)
            <div class="flex flex-col gap-3">
                <h3 class="text-lg font-semibold">{{ $group }}</h3>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($reports as $type => $report)
                        <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-md bg-jamu-secondary-light text-jamu-primary-dark">
                                    <span class="font-bold">L</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold">{{ $report['title'] }}</h4>
                                    <p class="mt-1 text-sm text-jamu-muted">{{ $report['description'] }}</p>
                                </div>
                            </div>
                            <a href="{{ route('laporan.show', $type) }}" class="mt-4 inline-flex rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">
                                Buka Laporan
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>
</x-layouts.app>
