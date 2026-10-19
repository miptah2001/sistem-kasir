@extends('layouts.app')

@section('title', 'Laporan Stok')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .sidebar, .topbar { display: none !important; }
        .main-content { margin-left: 0 !important; }
        body { background: #fff !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 no-print">
    <div>
        <h5 class="mb-0 fw-bold">Laporan Stok Produk</h5>
        <small class="text-muted">Status stok terkini</small>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary">
        <i class="bi bi-printer"></i> Print
    </button>
</div>

{{-- ============ STATISTIK ============ --}}
<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Total Produk</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($stats['total_products']) }}</h3>
                        <small class="text-muted">SKU terdaftar</small>
                    </div>
                    <div class="rounded-circle bg-primary-subtle p-3">
                        <i class="bi bi-box-seam text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Total Unit Stok</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($stats['total_stock']) }}</h3>
                        <small class="text-muted">unit</small>
                    </div>
                    <div class="rounded-circle bg-success-subtle p-3">
                        <i class="bi bi-boxes text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Nilai Modal</small>
                        <h5 class="fw-bold mb-0 mt-1 text-primary">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</h5>
                        <small class="text-muted">harga beli</small>
                    </div>
                    <div class="rounded-circle bg-info-subtle p-3">
                        <i class="bi bi-cash-stack text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Nilai Jual</small>
                        <h5 class="fw-bold mb-0 mt-1 text-success">Rp {{ number_format($stats['total_sell_value'], 0, ',', '.') }}</h5>
                        <small class="text-muted">harga jual</small>
                    </div>
                    <div class="rounded-circle bg-warning-subtle p-3">
                        <i class="bi bi-tags text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ ALERT STOK RENDAH ============ --}}
@if($stats['low_stock'] > 0 || $stats['out_of_stock'] > 0)
<div class="alert alert-warning d-flex align-items-center no-print">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
    <div>
        <strong>Perhatian!</strong>
        @if($stats['out_of_stock'] > 0)
            <span class="badge bg-danger">{{ $stats['out_of_stock'] }}</span> produk <b>habis</b>,
        @endif
        @if($stats['low_stock'] > 0)
            <span class="badge bg-warning text-dark">{{ $stats['low_stock'] }}</span> produk <b>stok rendah</b> (≤ 5).
        @endif
        Segera lakukan restock!
    </div>
</div>
@endif

{{-- ============ FILTER ============ --}}
<div class="card card-stat mb-3 no-print">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kategori</label>
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category') == $c->id)>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status Stok</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="safe" @selected(request('status') === 'safe')>Aman (> 5)</option>
                    <option value="low" @selected(request('status') === 'low')>Rendah (≤ 5)</option>
                    <option value="out" @selected(request('status') === 'out')>Habis (0)</option>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- ============ TABEL PRODUK ============ --}}
<div class="card card-stat">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-list-ul"></i> Daftar Stok Produk
            <span class="badge bg-secondary">{{ $products->count() }} produk</span>
        </h6>

        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>SKU</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th class="text-end">Modal</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Stok</th>
                        <th class="text-end">Nilai Stok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><code>{{ $p->sku }}</code></td>
                        <td class="fw-semibold">{{ $p->name }}</td>
                        <td>
                            <span class="badge bg-info-subtle text-info-emphasis">
                                {{ $p->category->name }}
                            </span>
                        </td>
                        <td class="text-end">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $p->stock }}</span>
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($p->stock * $p->cost_price, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($p->stock == 0)
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> Habis
                                </span>
                            @elseif($p->stock <= 5)
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-exclamation-triangle"></i> Rendah
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Aman
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada produk sesuai filter
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($products->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL</td>
                        <td class="text-center">{{ number_format($products->sum('stock')) }}</td>
                        <td class="text-end">Rp {{ number_format($products->sum(fn($p) => $p->stock * $p->cost_price), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection