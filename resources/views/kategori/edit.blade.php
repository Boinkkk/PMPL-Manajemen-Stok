@extends('layouts.app')

@section('title', 'Ubah Kategori')

@section('content')
<div class="content-card p-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1">Ubah Kategori</h4>
            <p class="text-muted mb-0">Perbarui data kategori produk sesuai kebutuhan.</p>
        </div>
        <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary-custom">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-modern mb-4">
            <h6 class="fw-semibold mb-3">Periksa kembali form:</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kategori.update', $kategori) }}" method="POST" class="js-loading-form">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nama_kategori" class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" placeholder="Masukkan nama kategori">
            @error('nama_kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="5" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Deskripsi kategori (opsional)">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex flex-column flex-sm-row gap-3">
            <button type="submit" class="btn btn-gold">
                <i class="fa-solid fa-save me-2"></i> Simpan Perubahan
            </button>
            <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary-custom">Batal</a>
        </div>
    </form>
</div>
@endsection