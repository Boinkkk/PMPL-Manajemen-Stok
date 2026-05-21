<x-layouts.app title="Tambah Pengguna">
    <section class="flex flex-col gap-4" x-data="passwordStrengthForm()">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Tambah Pengguna</h2>
                <p class="text-sm text-jamu-muted">Akun hanya dapat dibuat oleh Administrator.</p>
            </div>
            <a href="{{ route('pengguna.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <form method="POST" action="{{ route('pengguna.store') }}" class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-5 md:grid-cols-2">
            @csrf

            <x-form.input name="nama_lengkap" label="Nama Lengkap" required />
            <x-form.input name="username" label="Username" required />
            <x-form.input name="email" label="Email" type="email" required />

            <x-form.select name="id_role" label="Role" required>
                <option value="">Pilih role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id_role }}" @selected(old('id_role') == $role->id_role)>{{ $role->nama_role }}</option>
                @endforeach
            </x-form.select>

            <x-form.select name="status" label="Status" required>
                <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status') === 'nonaktif')>Nonaktif</option>
            </x-form.select>

            <div class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-jamu-text">Password</span>
                <div class="flex rounded-md border border-jamu-border bg-white shadow-sm focus-within:border-jamu-secondary focus-within:ring-1 focus-within:ring-jamu-secondary">
                    <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm focus:ring-0">
                    <button type="button" x-on:click="showPassword = !showPassword" class="px-3 text-sm font-medium text-jamu-primary">
                        <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></span>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span>Kekuatan:</span>
                    <span class="font-semibold" :class="strengthClass()" x-text="strengthLabel()"></span>
                </div>
                <x-form.error name="password" />
            </div>

            <x-form.input name="password_confirmation" label="Konfirmasi Password" type="password" required />

            <div class="flex justify-end gap-3 md:col-span-2">
                <a href="{{ route('pengguna.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Batal</a>
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Simpan Pengguna</button>
            </div>
        </form>
    </section>

    <script>
        function passwordStrengthForm() {
            return {
                showPassword: false,
                password: '',
                score() {
                    let score = 0;
                    if (this.password.length >= 8) score++;
                    if (/[A-Z]/.test(this.password) && /[a-z]/.test(this.password)) score++;
                    if (/\d/.test(this.password)) score++;
                    if (/[^A-Za-z0-9]/.test(this.password)) score++;
                    return score;
                },
                strengthLabel() {
                    if (this.score() >= 4) return 'Kuat';
                    if (this.score() >= 2) return 'Sedang';
                    return 'Lemah';
                },
                strengthClass() {
                    if (this.score() >= 4) return 'text-green-700';
                    if (this.score() >= 2) return 'text-yellow-700';
                    return 'text-red-700';
                },
            };
        }
    </script>
</x-layouts.app>
