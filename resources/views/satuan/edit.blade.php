@extends('layouts.app')

@section('title', 'Edit Satuan')

@section('content')
<div class="content-card p-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1">Edit Satuan</h4>
            <p class="text-muted mb-0">Perbarui informasi satuan produk.</p>
        </div>
        <a href="{{ route('satuan.index') }}" class="btn btn-back">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('satuan.update', $satuan) }}" method="POST" class="js-loading-form">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nama_satuan" class="form-label">Nama Satuan</label>
            <input type="text" name="nama_satuan" id="nama_satuan" class="form-control @error('nama_satuan') is-invalid @enderror" value="{{ old('nama_satuan', $satuan->nama_satuan) }}" placeholder="Masukkan nama satuan">
            @error('nama_satuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="singkatan" class="form-label">Singkatan</label>
            <input type="text" name="singkatan" id="singkatan" class="form-control @error('singkatan') is-invalid @enderror" value="{{ old('singkatan', $satuan->singkatan) }}" placeholder="Masukkan singkatan satuan">
            @error('singkatan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex flex-column flex-sm-row gap-3">
            <button type="submit" class="btn btn-gold">
                <i class="fa-solid fa-save me-2"></i> Perbarui Satuan
            </button>
            <a href="{{ route('satuan.index') }}" class="btn btn-back">
                <i class="fa-solid fa-xmark me-2"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection
