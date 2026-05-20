@extends('layouts.app')

@section('title', 'Ajukan Retur Produk')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Ajukan Retur Produk</h4>
            <p class="text-muted mb-0">Isi formulir retur untuk pengembalian produk cacat.</p>
        </div>
        <a href="{{ route('retur.index') }}" class="btn btn-outline-secondary-custom btn-sm">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('retur.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
        @csrf

        <div class="col-md-6">
            <label class="form-label fw-semibold">Distributor</label>
            <select name="id_distributor" class="form-select">
                <option value="">Pilih distributor</option>
                @foreach($distributors as $distributor)
                    <option value="{{ $distributor->id_distributor }}" {{ old('id_distributor') == $distributor->id_distributor ? 'selected' : '' }}>{{ $distributor->nama_distributor }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Produk</label>
            <select name="id_produk" class="form-select">
                <option value="">Pilih produk</option>
                @foreach($produks as $produk)
                    <option value="{{ $produk->id_produk }}" {{ old('id_produk') == $produk->id_produk ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Jumlah Retur</label>
            <input type="number" name="jumlah_retur" value="{{ old('jumlah_retur') }}" min="1" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Supplier (opsional)</label>
            <select name="id_supplier" class="form-select">
                <option value="">Gunakan supplier default</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id_supplier }}" {{ old('id_supplier') == $supplier->id_supplier ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Alasan</label>
            <textarea name="alasan" rows="4" class="form-control">{{ old('alasan') }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Foto Bukti</label>
            <input type="file" name="foto_bukti" accept="image/*" class="form-control">
        </div>

        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-gold">
                <i class="fa-solid fa-paper-plane me-2"></i> Kirim Retur
            </button>
            <a href="{{ route('retur.index') }}" class="btn btn-outline-secondary-custom">Batal</a>
        </div>
    </form>
</div>
@endsection
