@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h5 class="fw-bold mb-0">Detail Produk</h5>
        <small class="text-muted">{{ $product->name }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card card-stat">
            <div class="card-body text-center">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" class="img-fluid rounded">
                @else
                    <i class="bi bi-image text-muted" style="font-size:6rem;"></i>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-stat">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th width="180">SKU</th><td><code>{{ $product->sku }}</code></td></tr>
                    <tr><th>Nama</th><td class="fw-semibold">{{ $product->name }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $product->category->name }}</td></tr>
                    <tr><th>Harga Modal</th><td>Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td></tr>
                    <tr><th>Harga Jual</th><td class="fw-bold text-primary fs-5">Rp {{ number_format($product->price, 0, ',', '.') }}</td></tr>
                    <tr>
                        <th>Stok</th>
                        <td>
                            <span class="badge bg-{{ $product->stock <= 5 ? 'danger' : 'success' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                    </tr>
                    <tr><th>Status</th>
                        <td>
                            <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection