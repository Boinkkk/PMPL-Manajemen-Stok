<x-layouts.app title="Ubah Pengguna">
    <section class="flex flex-col gap-4" x-data="passwordStrengthForm()">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Ubah Pengguna</h2>
                <p class="text-sm text-jamu-muted">Perbarui data akun pengguna internal.</p>
            </div>
            <a href="{{ route('pengguna.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        @if ($isSelf)
            <x-alert type="warning" message="Anda tidak dapat mengubah role dan status akun Anda sendiri." />
        @endif

        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}" class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <x-form.input name="nama_lengkap" label="Nama Lengkap" :value="$pengguna->nama_lengkap" required />
            <x-form.input name="username" label="Username" :value="$pengguna->username" required />
            <x-form.input name="email" label="Email" type="email" :value="$pengguna->email" required />

            <x-form.select name="id_role" label="Role" required :disabled="$isSelf">
                @foreach ($roles as $role)
                    <option value="{{ $role->id_role }}" @selected(old('id_role', $pengguna->id_role) == $role->id_role)>{{ $role->nama_role }}</option>
                @endforeach
            </x-form.select>

            <x-form.select name="status" label="Status" required :disabled="$isSelf">
                <option value="aktif" @selected(old('status', $pengguna->status) === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status', $pengguna->status) === 'nonaktif')>Nonaktif</option>
            </x-form.select>

            <div class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-jamu-text">Password Baru</span>
                <div class="flex rounded-md border border-jamu-border bg-white shadow-sm focus-within:border-jamu-secondary focus-within:ring-1 focus-within:ring-jamu-secondary">
                    <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm focus:ring-0">
                    <button type="button" x-on:click="showPassword = !showPassword" class="px-3 text-sm font-medium text-jamu-primary">
                        <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></span>
                    </button>
                </div>
                <p class="text-xs text-jamu-muted">Kosongkan jika tidak ingin mengubah password.</p>
                <div class="flex items-center gap-2 text-xs" x-show="password.length > 0">
                    <span>Kekuatan:</span>
                    <span class="font-semibold" :class="strengthClass()" x-text="strengthLabel()"></span>
                </div>
                <x-form.error name="password" />
            </div>

            <x-form.input name="password_confirmation" label="Konfirmasi Password Baru" type="password" />

            <div class="flex justify-end gap-3 md:col-span-2">
                <a href="{{ route('pengguna.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Batal</a>
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Simpan Perubahan</button>
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
