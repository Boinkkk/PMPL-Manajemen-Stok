<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register Testing - Manajemen Stok Jamu Madura</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-jamu-bg font-sans text-jamu-text">
        <main class="flex min-h-screen items-center justify-center px-4 py-8">
            <section class="w-full max-w-2xl rounded-md border border-jamu-border bg-jamu-surface p-6 shadow-sm" x-data="passwordStrengthForm()">
                <div class="mb-6 text-center">
                    <p class="text-sm font-semibold text-jamu-secondary">Khusus Testing</p>
                    <h1 class="mt-1 text-2xl font-bold text-jamu-primary">Register Akun Testing</h1>
                    <p class="mt-2 text-sm text-jamu-muted">Halaman ini terbuka untuk kebutuhan uji coba role dan akses sistem.</p>
                </div>

                <x-alert />

                <form method="POST" action="{{ route('register.proses') }}" class="mt-6 grid gap-4 md:grid-cols-2">
                    @csrf

                    <x-form.input name="nama_lengkap" label="Nama Lengkap" required />
                    <x-form.input name="username" label="Username" required />
                    <x-form.input name="email" label="Email" type="email" required />

                    <x-form.select name="id_role" label="Role Testing" required>
                        <option value="">Pilih role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id_role }}" @selected(old('id_role') == $role->id_role)>{{ $role->nama_role }}</option>
                        @endforeach
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

                    <div class="flex flex-col gap-3 md:col-span-2 md:flex-row md:items-center md:justify-between">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-jamu-primary hover:underline">Sudah punya akun? Login</a>
                        <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                            Buat Akun Testing
                        </button>
                    </div>
                </form>
            </section>
        </main>

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
    </body>
</html>
