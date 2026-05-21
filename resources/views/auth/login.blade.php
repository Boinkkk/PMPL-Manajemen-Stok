<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - Manajemen Stok Jamu Madura</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-jamu-bg font-sans text-jamu-text">
        <main class="flex min-h-screen items-center justify-center px-4 py-8">
            <section class="w-full max-w-md rounded-md border border-jamu-border bg-jamu-surface p-6 shadow-sm" x-data="{ showPassword: false, loading: false }">
                <div class="mb-6 text-center">
                    <p class="text-sm font-semibold text-jamu-secondary">Sistem Informasi</p>
                    <h1 class="mt-1 text-2xl font-bold text-jamu-primary">Manajemen Stok Jamu Madura</h1>
                    <p class="mt-2 text-sm text-jamu-muted">Masuk menggunakan akun yang diberikan Administrator.</p>
                </div>

                <x-alert />

                <form method="POST" action="{{ route('login.proses') }}" class="mt-6 flex flex-col gap-4" x-on:submit="loading = true">
                    @csrf

                    <x-form.input name="username" label="Username" :value="old('username')" required autofocus />

                    <label class="flex flex-col gap-1 text-sm">
                        <span class="font-medium text-jamu-text">Password</span>
                        <div class="flex rounded-md border border-jamu-border bg-white shadow-sm focus-within:border-jamu-secondary focus-within:ring-1 focus-within:ring-jamu-secondary">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm focus:ring-0"
                            >
                            <button type="button" x-on:click="showPassword = !showPassword" class="px-3 text-sm font-medium text-jamu-primary">
                                <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></span>
                            </button>
                        </div>
                        <x-form.error name="password" />
                    </label>

                    <button
                        type="submit"
                        class="rounded-md bg-jamu-secondary px-4 py-2.5 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light disabled:cursor-not-allowed disabled:opacity-70"
                        :disabled="loading"
                    >
                        <span x-show="!loading">Login</span>
                        <span x-show="loading">Memproses...</span>
                    </button>

                    <p class="text-center text-sm text-jamu-muted">
                        Butuh akun testing?
                        <a href="{{ route('register') }}" class="font-semibold text-jamu-primary hover:underline">Daftar di sini</a>
                    </p>
                </form>
            </section>
        </main>
    </body>
</html>
