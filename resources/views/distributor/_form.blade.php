@props([
    'distributor' => null,
    'kodeDistributor' => null,
    'action',
    'method' => 'POST',
])

@php
    $isEdit = filled($distributor);
@endphp

<form
    method="POST"
    action="{{ $action }}"
    class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-5"
    x-data="distributorForm({
        nama: @js(old('nama_distributor', $distributor?->nama_distributor ?? '')),
        email: @js(old('email', $distributor?->email ?? '')),
        ignoreId: @js($distributor?->id_distributor),
    })"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label class="flex flex-col gap-1 text-sm">
            <span class="font-medium text-jamu-text">Kode Distributor</span>
            <input type="text" value="{{ $distributor?->kode_distributor ?? $kodeDistributor }}" readonly class="rounded-md border-jamu-border bg-jamu-bg px-3 py-2 text-sm text-jamu-muted">
            <span class="text-xs text-jamu-muted">Kode dibuat otomatis dan tidak dapat diubah.</span>
        </label>

        <label class="flex flex-col gap-1 text-sm">
            <span class="font-medium text-jamu-text">Nama Distributor <span class="text-red-600">*</span></span>
            <input type="text" name="nama_distributor" x-model.debounce.400ms="nama" @input.debounce.450ms="checkDuplicate()" value="{{ old('nama_distributor', $distributor?->nama_distributor) }}" required class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary">
            <template x-if="duplicate.nama_exists">
                <span class="text-xs text-red-700">Nama distributor sudah terdaftar.</span>
            </template>
            <template x-if="nama.length >= 3 && !duplicate.nama_exists">
                <span class="text-xs text-green-700">Nama distributor tersedia.</span>
            </template>
            <x-form.error name="nama_distributor" />
        </label>
    </div>

    <label class="flex flex-col gap-1 text-sm">
        <span class="font-medium text-jamu-text">Alamat</span>
        <textarea name="alamat" rows="4" maxlength="500" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary">{{ old('alamat', $distributor?->alamat) }}</textarea>
        <x-form.error name="alamat" />
    </label>

    <div class="grid gap-4 md:grid-cols-3">
        <x-form.input name="telepon" label="Telepon" :value="$distributor?->telepon" placeholder="08xx / +62xx / (031)xxx" />

        <label class="flex flex-col gap-1 text-sm">
            <span class="font-medium text-jamu-text">Email</span>
            <input type="email" name="email" x-model.debounce.400ms="email" @input.debounce.450ms="checkDuplicate()" value="{{ old('email', $distributor?->email) }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary">
            <template x-if="duplicate.email_exists">
                <span class="text-xs text-red-700">Email distributor sudah digunakan.</span>
            </template>
            <template x-if="email.length > 3 && !duplicate.email_exists">
                <span class="text-xs text-green-700">Email dapat digunakan.</span>
            </template>
            <x-form.error name="email" />
        </label>

        <x-form.input name="kontak_person" label="Kontak Person" :value="$distributor?->kontak_person" />
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ $distributor ? route('distributor.show', $distributor) : route('distributor.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Batal</a>
        <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Distributor' }}
        </button>
    </div>
</form>

<script>
    function distributorForm(initial) {
        return {
            nama: initial.nama || '',
            email: initial.email || '',
            ignoreId: initial.ignoreId,
            duplicate: {
                nama_exists: false,
                email_exists: false,
            },
            async checkDuplicate() {
                const params = new URLSearchParams();
                if (this.nama) params.set('nama_distributor', this.nama);
                if (this.email) params.set('email', this.email);
                if (this.ignoreId) params.set('ignore_id', this.ignoreId);

                const response = await fetch(`{{ route('distributor.check-duplicate') }}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const json = await response.json();
                this.duplicate = json.data || this.duplicate;
            },
        };
    }
</script>
