<x-layouts.app title="Audit Trail">
    <section
        class="flex flex-col gap-5"
        x-data="auditTrailPage()"
    >
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Audit Trail</h2>
                <p class="text-sm text-jamu-muted">Riwayat perubahan data dan aktivitas sensitif pada sistem.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="submitExport('{{ route('audit.export.csv') }}')" class="rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    Ekspor CSV
                </button>
                <button type="button" @click="submitExport('{{ route('audit.export.pdf') }}')" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Ekspor PDF
                </button>
            </div>
        </div>

        <form x-ref="filterForm" id="filterForm" method="GET" action="{{ route('audit.index') }}" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-5">
            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium">Pengguna</span>
                <select name="id_pengguna" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua Pengguna</option>
                    @foreach ($penggunaList as $pengguna)
                        <option value="{{ $pengguna->id_pengguna }}" @selected(request('id_pengguna') == $pengguna->id_pengguna)>
                            {{ $pengguna->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium">Tanggal Mulai</span>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            </label>

            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium">Tanggal Selesai</span>
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            </label>

            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium">Aktivitas</span>
                <select name="aksi" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua Aktivitas</option>
                    @foreach ($aksiList as $aksi)
                        <option value="{{ $aksi }}" @selected(request('aksi') === $aksi)>{{ $aksi }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Filter</button>
                <a href="{{ route('audit.index') }}" class="flex-1 rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
            </div>
        </form>

        <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
            <div class="mb-3 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="font-semibold">Daftar Aktivitas</h3>
                    <p class="text-sm text-jamu-muted">Menampilkan {{ number_format($audits->total(), 0, ',', '.') }} catatan audit.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-jamu-border text-sm">
                    <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Pengguna</th>
                            <th class="px-4 py-3">Aktivitas</th>
                            <th class="px-4 py-3">Modul</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-jamu-border">
                        @forelse ($audits as $audit)
                            <tr class="hover:bg-jamu-bg">
                                <td class="px-4 py-3">{{ $audits->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3">{{ $audit->waktu_aksi?->locale('id')->translatedFormat('d F Y H:i:s') }}</td>
                                <td class="px-4 py-3">{{ $audit->pengguna?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $audit->aksi }}</td>
                                <td class="px-4 py-3">{{ $audit->modul }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Berhasil</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" @click="showDetail({{ $audit->id_audit }})" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-jamu-muted">Belum ada data audit trail.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $audits->links() }}
            </div>
        </div>

        <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="modalOpen = false" class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-md border border-jamu-border bg-jamu-surface shadow-xl">
                <div class="flex items-center justify-between border-b border-jamu-border px-5 py-4">
                    <h3 class="font-semibold">Detail Aktivitas</h3>
                    <button type="button" @click="modalOpen = false" class="rounded-md border border-jamu-border px-3 py-1 text-sm hover:bg-jamu-bg">Tutup</button>
                </div>
                <div class="grid gap-4 p-5 text-sm md:grid-cols-2">
                    <template x-if="loading">
                        <div class="md:col-span-2 text-center text-jamu-muted">Memuat detail...</div>
                    </template>
                    <template x-if="!loading">
                        <div class="contents">
                            <div><p class="text-xs text-jamu-muted">ID Aktivitas</p><p class="font-medium" x-text="`AT-${detail.id ?? '-'}`"></p></div>
                            <div><p class="text-xs text-jamu-muted">Pengguna</p><p class="font-medium" x-text="detail.pengguna ?? '-'"></p></div>
                            <div><p class="text-xs text-jamu-muted">Modul</p><p class="font-medium" x-text="detail.modul ?? '-'"></p></div>
                            <div><p class="text-xs text-jamu-muted">Aktivitas</p><p class="font-medium" x-text="detail.aktivitas ?? '-'"></p></div>
                            <div class="md:col-span-2"><p class="text-xs text-jamu-muted">Data Sebelum</p><pre class="mt-1 overflow-x-auto rounded-md bg-jamu-bg p-3 text-xs" x-text="formatData(detail.data_lama)"></pre></div>
                            <div class="md:col-span-2"><p class="text-xs text-jamu-muted">Data Sesudah</p><pre class="mt-1 overflow-x-auto rounded-md bg-jamu-bg p-3 text-xs" x-text="formatData(detail.data_baru)"></pre></div>
                            <div><p class="text-xs text-jamu-muted">Waktu</p><p class="font-medium" x-text="detail.waktu ?? '-'"></p></div>
                            <div><p class="text-xs text-jamu-muted">IP Address</p><p class="font-medium" x-text="detail.ip ?? '-'"></p></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <script>
        function auditTrailPage() {
            return {
                modalOpen: false,
                loading: false,
                detail: {},
                submitExport(action) {
                    const form = this.$refs.filterForm;
                    const defaultAction = form.action;
                    form.action = action;
                    form.submit();
                    form.action = defaultAction;
                },
                async showDetail(id) {
                    this.modalOpen = true;
                    this.loading = true;
                    this.detail = {};
                    const response = await fetch(`/audit/detail/${id}`, { headers: { 'Accept': 'application/json' } });
                    this.detail = await response.json();
                    this.loading = false;
                },
                formatData(data) {
                    if (!data || Object.keys(data).length === 0) {
                        return '-';
                    }
                    return JSON.stringify(data, null, 2);
                },
            };
        }
    </script>
</x-layouts.app>
