@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Tambah Produk</h4>
            <p class="text-muted mb-0">Isi detail produk untuk menambah produk baru.</p>
        </div>
        <a href="{{ route('produk.index') }}" class="btn btn-back">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('produk.store') }}" method="POST" class="js-loading-form mt-4">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="kode_produk" class="form-label">Kode Produk</label>
                <input type="text" name="kode_produk" id="kode_produk" class="form-control @error('kode_produk') is-invalid @enderror" value="{{ old('kode_produk', $generatedKode) }}">
                @error('kode_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" name="nama_produk" id="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk') }}">
                @error('nama_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="id_kategori" class="form-label">Kategori</label>
                <select name="id_kategori" id="id_kategori" class="form-select @error('id_kategori') is-invalid @enderror">
                    <option value="">Pilih Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id_kategori }}" {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
                @error('id_kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="id_satuan" class="form-label">Satuan</label>
                <select name="id_satuan" id="id_satuan" class="form-select @error('id_satuan') is-invalid @enderror">
                    <option value="">Pilih Satuan</option>
                    @foreach($satuans as $s)
                        <option value="{{ $s->id_satuan }}" {{ old('id_satuan') == $s->id_satuan ? 'selected' : '' }}>{{ $s->nama_satuan }}</option>
                    @endforeach
                </select>
                @error('id_satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                <input type="number" step="0.01" name="harga_satuan" id="harga_satuan" class="form-control @error('harga_satuan') is-invalid @enderror" value="{{ old('harga_satuan', 0) }}">
                @error('harga_satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="stok_terkini" class="form-label">Stok Terkini</label>
                <input type="number" name="stok_terkini" id="stok_terkini" class="form-control @error('stok_terkini') is-invalid @enderror" value="{{ old('stok_terkini', 0) }}">
                @error('stok_terkini')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="stok_minimum" class="form-label">Stok Minimum</label>
                <input type="number" name="stok_minimum" id="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" value="{{ old('stok_minimum', 0) }}">
                @error('stok_minimum')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('produk.index') }}" class="btn btn-back">
                <i class="fa-solid fa-xmark me-2"></i> Batal
            </a>
            <button type="submit" class="btn btn-gold">
                <i class="fa-solid fa-save me-2"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection
