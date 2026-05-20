<x-layouts.app title="Detail Retur Produk">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-2xl font-semibold">Detail Retur #{{ $retur->id_retur }}</h2>
                    @if($retur->status === 'pending')
                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">Pending</span>
                    @elseif($retur->status === 'disetujui')
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Disetujui</span>
                    @else
                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Ditolak</span>
                    @endif
                </div>
                <p class="text-sm text-jamu-muted">Detail pengajuan retur dan riwayat verifikasi.</p>
            </div>
            <a href="{{ route('retur.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-5">
                <h3 class="font-semibold">Informasi Retur</h3>
                <dl class="mt-4 grid gap-3 text-sm">
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Distributor</dt>
                        <dd class="font-medium sm:col-span-2">{{ $retur->distributor?->nama_distributor ?? '-' }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Produk</dt>
                        <dd class="font-medium sm:col-span-2">{{ $retur->produk?->nama_produk ?? '-' }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Jumlah Retur</dt>
                        <dd class="font-medium sm:col-span-2">{{ number_format($retur->jumlah_retur, 0, ',', '.') }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Alasan</dt>
                        <dd class="sm:col-span-2">{{ $retur->alasan }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Tanggal Lapor</dt>
                        <dd class="sm:col-span-2">{{ $retur->tanggal_lapor?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Supplier</dt>
                        <dd class="sm:col-span-2">{{ $retur->supplier?->nama_supplier ?? 'Default supplier' }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Admin Verifikator</dt>
                        <dd class="sm:col-span-2">{{ $retur->adminVerifikator?->nama_lengkap ?? '-' }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Alasan Penolakan</dt>
                        <dd class="sm:col-span-2">{{ $retur->alasan_penolakan ?? '-' }}</dd>
                    </div>
                    <div class="grid gap-1 sm:grid-cols-3">
                        <dt class="text-jamu-muted">Tanggal Selesai</dt>
                        <dd class="sm:col-span-2">{{ $retur->tanggal_selesai?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-md border border-jamu-border bg-jamu-surface p-5">
                <h3 class="font-semibold">Bukti Foto</h3>
                <div class="mt-4">
                    @if($retur->foto_bukti)
                        <img src="{{ asset('storage/'.$retur->foto_bukti) }}" alt="Bukti retur" class="max-h-96 w-full rounded-md border border-jamu-border object-cover">
                    @else
                        <div class="rounded-md border border-dashed border-jamu-border bg-jamu-bg p-10 text-center text-sm text-jamu-muted">Tidak ada foto bukti.</div>
                    @endif
                </div>
            </div>
        </div>

        @if($retur->status === 'pending')
            <div class="grid gap-4 lg:grid-cols-2">
                <form action="{{ route('retur.setujui', $retur) }}" method="POST" class="rounded-md border border-green-200 bg-green-50 p-5" onsubmit="return confirm('Setujui retur ini dan kurangi stok produk terkait?')">
                    @csrf
                    <h3 class="font-semibold text-green-900">Setujui Retur</h3>
                    <p class="mt-2 text-sm text-green-800">Retur yang disetujui akan mengurangi stok produk terkait.</p>
                    <button type="submit" class="mt-4 rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Setujui</button>
                </form>

                <form action="{{ route('retur.tolak', $retur) }}" method="POST" class="rounded-md border border-red-200 bg-red-50 p-5">
                    @csrf
                    <h3 class="font-semibold text-red-900">Tolak Retur</h3>
                    <label class="mt-3 flex flex-col gap-1 text-sm">
                        <span class="font-medium text-red-900">Alasan Penolakan</span>
                        <textarea name="alasan_penolakan" rows="4" class="rounded-md border-red-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-red-400 focus:ring-red-400" required>{{ old('alasan_penolakan') }}</textarea>
                        <x-form.error name="alasan_penolakan" />
                    </label>
                    <button type="submit" class="mt-4 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tolak</button>
                </form>
            </div>
        @endif

        <div class="rounded-md border border-jamu-border bg-jamu-surface p-5">
            <div class="mb-4">
                <h3 class="font-semibold">Riwayat Audit</h3>
                <p class="text-sm text-jamu-muted">Aktivitas perubahan pada data retur ini.</p>
            </div>

            <x-table>
                <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                    <tr>
                        <th class="px-4 py-3">Aksi</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Modul</th>
                        <th class="px-4 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-jamu-border">
                    @forelse($auditTrail as $audit)
                        <tr class="hover:bg-jamu-bg">
                            <td class="px-4 py-3 font-medium">{{ $audit->aksi }}</td>
                            <td class="px-4 py-3">{{ $audit->pengguna?->nama_lengkap ?? 'Sistem' }}</td>
                            <td class="px-4 py-3 text-jamu-muted">{{ $audit->modul }}</td>
                            <td class="px-4 py-3 text-jamu-muted">{{ $audit->waktu_aksi?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-jamu-muted">Belum ada audit untuk retur ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
    </section>
</x-layouts.app>
