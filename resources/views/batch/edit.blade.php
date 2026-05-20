@extends('layouts.app')

@section('title', 'Edit Batch')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Edit Batch</h4>
            <p class="text-muted mb-0">Perbarui informasi batch produk.</p>
        </div>
        <a href="{{ route('batch.index') }}" class="btn btn-back">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('batch.update', $batch) }}" method="POST" class="js-loading-form mt-4">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_produk" class="form-label">Produk</label>
                <select name="id_produk" id="id_produk" class="form-select @error('id_produk') is-invalid @enderror">
                    <option value="">Pilih Produk</option>
                    @foreach($produks as $produk)
                        <option value="{{ $produk->id_produk }}" {{ old('id_produk', $batch->id_produk) == $produk->id_produk ? 'selected' : '' }}>
                            {{ $produk->kode_produk }} - {{ $produk->nama_produk }}
                        </option>
                    @endforeach
                </select>
                @error('id_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="nomor_batch" class="form-label">Nomor Batch</label>
                <input type="text" name="nomor_batch" id="nomor_batch" class="form-control @error('nomor_batch') is-invalid @enderror" value="{{ old('nomor_batch', $batch->nomor_batch) }}">
                @error('nomor_batch')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="tanggal_produksi" class="form-label">Tanggal Produksi</label>
                <input type="date" name="tanggal_produksi" id="tanggal_produksi" class="form-control @error('tanggal_produksi') is-invalid @enderror" value="{{ old('tanggal_produksi', $batch->tanggal_produksi?->format('Y-m-d')) }}">
                @error('tanggal_produksi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="tanggal_expired" class="form-label">Tanggal Expired</label>
                <input type="date" name="tanggal_expired" id="tanggal_expired" class="form-control @error('tanggal_expired') is-invalid @enderror" value="{{ old('tanggal_expired', $batch->tanggal_expired->format('Y-m-d')) }}">
                @error('tanggal_expired')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="4">{{ old('keterangan', $batch->keterangan) }}</textarea>
                @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('batch.index') }}" class="btn btn-back">
                <i class="fa-solid fa-xmark me-2"></i> Batal
            </a>
            <button type="submit" class="btn btn-gold">
                <i class="fa-solid fa-save me-2"></i> Perbarui
            </button>
        </div>
    </form>
</div>
@endsection
