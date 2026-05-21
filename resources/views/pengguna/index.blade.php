<x-layouts.app title="Manajemen Pengguna">
    <section class="flex flex-col gap-4" x-data="{ modalOpen: false, modalName: '', modalAction: '' }">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Manajemen Pengguna</h2>
                <p class="text-sm text-jamu-muted">Kelola akun internal. Tidak ada registrasi publik.</p>
            </div>
            <a href="{{ route('pengguna.create') }}" class="inline-flex items-center justify-center rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                Tambah Pengguna
            </a>
        </div>

        <form method="GET" action="{{ route('pengguna.index') }}" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-4">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, username, email" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">

            <select name="id_role" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id_role }}" @selected(($filters['id_role'] ?? '') == $role->id_role)>{{ $role->nama_role }}</option>
                @endforeach
            </select>

            <select name="status" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua status</option>
                <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(($filters['status'] ?? '') === 'nonaktif')>Nonaktif</option>
            </select>

            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">
                Terapkan Filter
            </button>
        </form>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Dibuat</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse ($pengguna as $item)
                    @php($isSelf = (int) auth()->id() === (int) $item->getKey())
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-medium">{{ $item->nama_lengkap }}</span>
                                @if ($isSelf)
                                    <span class="rounded-md bg-jamu-secondary-light px-2 py-1 text-xs font-semibold text-jamu-primary-dark">Anda</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $item->username }}</td>
                        <td class="px-4 py-3">{{ $item->email }}</td>
                        <td class="px-4 py-3"><x-badge :status="$item->role?->nama_role" /></td>
                        <td class="px-4 py-3"><x-badge :status="$item->status" /></td>
                        <td class="px-4 py-3">{{ $item->dibuat_pada_formatted ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('pengguna.edit', $item) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Edit</a>
                                <button
                                    type="button"
                                    @disabled($isSelf || $item->status === 'nonaktif')
                                    x-on:click="modalOpen = true; modalName = @js($item->nama_lengkap); modalAction = @js(route('pengguna.nonaktifkan', $item));"
                                    class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    Nonaktifkan
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-jamu-muted">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $pengguna->links() }}

        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div x-on:click.outside="modalOpen = false" class="w-full max-w-md rounded-md border border-jamu-border bg-jamu-surface p-5 shadow-lg">
                <h3 class="text-lg font-semibold">Nonaktifkan Pengguna</h3>
                <p class="mt-2 text-sm text-jamu-muted">Akun <span class="font-semibold text-jamu-text" x-text="modalName"></span> akan dinonaktifkan dan tidak dapat login.</p>
                <form method="POST" :action="modalAction" class="mt-5 flex justify-end gap-3">
                    @csrf
                    @method('PATCH')
                    <button type="button" x-on:click="modalOpen = false" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Batal</button>
                    <button type="submit" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Nonaktifkan</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
