@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Daftar Produk</h5>
        <small class="text-muted">Total {{ $products->total() }} produk</small>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Produk
    </a>
</div>

<div class="card card-stat mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama atau SKU...">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="stock_filter" class="form-select">
                    <option value="">Semua Stok</option>
                    <option value="low" @selected(request('stock_filter') === 'low')>Stok Rendah</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary flex-fill"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse($products as $p)
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card card-stat h-100">
            <div style="height: 160px; overflow:hidden; background:#f8fafc;" class="d-flex align-items-center justify-content-center">
                @if($p->image)
                    <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}"
                         style="width:100%; height:100%; object-fit:cover;">
                @else
                    <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                @endif
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <small class="text-muted"><code>{{ $p->sku }}</code></small>
                    <span class="badge bg-info-subtle text-info-emphasis">{{ $p->category->name }}</span>
                </div>
                <h6 class="fw-semibold mb-2">{{ $p->name }}</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-primary">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                        <small class="text-muted">Modal: Rp {{ number_format($p->cost_price, 0, ',', '.') }}</small>
                    </div>
                    <span class="badge bg-{{ $p->stock <= 5 ? 'danger' : 'success' }}-subtle
                                 text-{{ $p->stock <= 5 ? 'danger' : 'success' }}-emphasis">
                        Stok: {{ $p->stock }}
                    </span>
                </div>
            </div>
            <div class="card-footer bg-white border-top d-flex gap-1">
                <a href="{{ route('products.show', $p) }}" class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-warning flex-fill">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('products.destroy', $p) }}" method="POST" class="flex-fill"
                      onsubmit="return confirm('Hapus produk {{ $p->name }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger w-100"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-stat">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">Belum ada produk</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-3">{{ $products->links() }}</div>
@endsection