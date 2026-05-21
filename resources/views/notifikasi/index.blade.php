<x-layouts.app title="Notifikasi">
    <section class="flex flex-col gap-5" x-data="{ allChecked: false }">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Notifikasi</h2>
                <p class="text-sm text-jamu-muted">Notifikasi stok minimum, stok habis, dan kedaluwarsa batch.</p>
            </div>
            <form method="POST" action="{{ route('notifikasi.mark-all-read') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                    Tandai Semua Sudah Dibaca
                </button>
            </form>
        </div>

        <form method="GET" action="{{ route('notifikasi.index') }}" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-5">
            <select name="jenis" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua jenis</option>
                @foreach (['stok_minimum' => 'Stok minimum', 'stok_habis' => 'Stok habis', 'kedaluwarsa' => 'Kedaluwarsa'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['jenis'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua status</option>
                <option value="belum_dibaca" @selected(($filters['status'] ?? '') === 'belum_dibaca')>Belum dibaca</option>
                <option value="dibaca" @selected(($filters['status'] ?? '') === 'dibaca')>Sudah dibaca</option>
            </select>
            <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white">Filter</button>
        </form>

        <form id="bulk-notif-form" method="POST" action="{{ route('notifikasi.bulk') }}">
            @csrf
        </form>

        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center gap-2 rounded-md border border-jamu-border bg-jamu-surface p-3">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" x-model="allChecked" @change="document.querySelectorAll('.notif-check').forEach((item) => item.checked = allChecked)" class="rounded border-jamu-border">
                    Pilih semua
                </label>
                <button form="bulk-notif-form" type="submit" name="action" value="baca" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Tandai Dibaca</button>
                @if (auth()->user()?->isAdministrator())
                    <button form="bulk-notif-form" type="submit" name="action" value="hapus" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                @endif
            </div>

            <div class="flex flex-col gap-4">
                @forelse ($groups as $groupName => $items)
                    <div class="rounded-md border border-jamu-border bg-jamu-surface">
                        <div class="border-b border-jamu-border px-4 py-3 font-semibold">{{ $groupName }}</div>
                        <div class="divide-y divide-jamu-border">
                            @foreach ($items as $item)
                                <div class="flex gap-3 px-4 py-3 {{ $item->isUnread() ? 'bg-blue-50/40 font-semibold' : '' }}">
                                    <input form="bulk-notif-form" type="checkbox" name="ids[]" value="{{ $item->id_notifikasi }}" class="notif-check mt-1 rounded border-jamu-border">
                                    <span class="mt-2 h-2 w-2 rounded-full {{ $item->isUnread() ? 'bg-blue-600' : 'bg-transparent' }}"></span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-md border px-2 py-1 text-xs font-semibold {{ $item->jenis === 'stok_habis' ? 'border-red-200 bg-red-50 text-red-800' : ($item->jenis === 'kedaluwarsa' ? 'border-orange-200 bg-orange-50 text-orange-800' : 'border-yellow-200 bg-yellow-50 text-yellow-800') }}">
                                                {{ str_replace('_', ' ', ucfirst($item->jenis)) }}
                                            </span>
                                            <span class="text-xs text-jamu-muted">{{ $item->waktu_relatif }}</span>
                                            <span class="text-xs text-jamu-muted">{{ $item->status === 'belum_dibaca' ? 'Belum dibaca' : 'Sudah dibaca' }}</span>
                                        </div>
                                        <p class="mt-2">{{ $item->pesan }}</p>
                                        <p class="mt-1 text-sm text-jamu-muted">{{ $item->produk?->nama_produk ?? '-' }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        @if ($item->isUnread())
                                            <form method="POST" action="{{ route('notifikasi.mark-read', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Tandai Dibaca</button>
                                            </form>
                                        @endif
                                        @if ($item->produk)
                                            <a href="{{ route('monitoring.products', ['q' => $item->produk->kode_produk]) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Lihat Produk</a>
                                        @endif
                                        @if (auth()->user()?->isAdministrator())
                                            <form method="POST" action="{{ route('notifikasi.destroy', $item) }}" onsubmit="return confirm('Hapus notifikasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-md border border-jamu-border bg-jamu-surface px-4 py-8 text-center text-jamu-muted">
                        Belum ada notifikasi.
                    </div>
                @endforelse
            </div>
        </div>

        {{ $notifikasi->links() }}
    </section>
</x-layouts.app>
