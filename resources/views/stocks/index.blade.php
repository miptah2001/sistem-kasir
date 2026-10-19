@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('content')

{{-- ===== HEADER ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Manajemen Stok</h5>
        <small class="text-muted">Catat stok masuk & keluar produk</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockModal">
        <i class="bi bi-plus-lg"></i> Catat Stok
    </button>
</div>

{{-- ===== STATISTIK ===== --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Stok Masuk</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($stats['total_in']) }}</h3>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i>
                            {{ number_format($stats['today_in']) }} hari ini
                        </small>
                    </div>
                    <div class="rounded-circle bg-success-subtle p-3">
                        <i class="bi bi-box-arrow-in-down text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Stok Keluar</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($stats['total_out']) }}</h3>
                        <small class="text-danger">
                            <i class="bi bi-arrow-down"></i>
                            {{ number_format($stats['today_out']) }} hari ini
                        </small>
                    </div>
                    <div class="rounded-circle bg-danger-subtle p-3">
                        <i class="bi bi-box-arrow-up text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Total Produk</small>
                        <h3 class="fw-bold mb-0 mt-1">{{ \App\Models\Product::count() }}</h3>
                        <small class="text-muted">SKU aktif</small>
                    </div>
                    <div class="rounded-circle bg-primary-subtle p-3">
                        <i class="bi bi-box-seam text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted fw-semibold">Stok Rendah</small>
                        <h3 class="fw-bold mb-0 mt-1 text-danger">
                            {{ \App\Models\Product::where('stock', '<=', 5)->count() }}
                        </h3>
                        <small class="text-muted">≤ 5 unit</small>
                    </div>
                    <div class="rounded-circle bg-warning-subtle p-3">
                        <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== FILTER ===== --}}
<div class="card card-stat mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="product_id" class="form-select">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Semua Tipe</option>
                    <option value="in" @selected(request('type') === 'in')>Masuk</option>
                    <option value="out" @selected(request('type') === 'out')>Keluar</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- ===== TABEL HISTORY ===== --}}
<div class="card card-stat">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th width="160">Waktu</th>
                        <th>Produk</th>
                        <th width="80" class="text-center">Tipe</th>
                        <th width="70" class="text-center">Qty</th>
                        <th width="150" class="text-center">Stok</th>
                        <th>Catatan</th>
                        <th width="140">User</th>
                        <th width="60"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                    <tr>
                        <td>
                            <div class="fw-semibold" style="font-size:.85rem;">
                                {{ $m->created_at->format('d M Y') }}
                            </div>
                            <small class="text-muted">{{ $m->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $m->product->name }}</div>
                            <small class="text-muted"><code>{{ $m->product->sku }}</code></small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $m->type_color }}-subtle text-{{ $m->type_color }}-emphasis">
                                <i class="bi bi-arrow-{{ $m->type === 'in' ? 'down' : 'up' }}"></i>
                                {{ $m->type_label }}
                            </span>
                        </td>
                        <td class="text-center fw-bold">
                            <span class="text-{{ $m->type_color }}">
                                {{ $m->type === 'in' ? '+' : '-' }}{{ $m->qty }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted">{{ $m->stock_before }}</span>
                            <i class="bi bi-arrow-right mx-1"></i>
                            <span class="fw-bold">{{ $m->stock_after }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $m->note ?? '—' }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width:28px;height:28px;font-size:.75rem;">
                                    {{ strtoupper(substr($m->user->name, 0, 1)) }}
                                </div>
                                <small>{{ $m->user->name }}</small>
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('stocks.destroy', $m) }}" method="POST"
                                  onsubmit="return confirm('Hapus riwayat ini? Stok produk TIDAK akan dikembalikan.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada pergerakan stok
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $movements->links() }}
        </div>
    </div>
</div>

{{-- ===== MODAL CATAT STOK ===== --}}
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('stocks.store') }}" id="stockForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-left-right"></i> Catat Pergerakan Stok
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Pilih Tipe --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Pergerakan <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="typeIn" value="in" checked>
                                <label class="btn btn-outline-success w-100" for="typeIn">
                                    <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="typeOut" value="out">
                                <label class="btn btn-outline-danger w-100" for="typeOut">
                                    <i class="bi bi-box-arrow-up"></i> Stok Keluar
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Pilih Produk --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                        <select name="product_id" id="productSelect" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                        data-stock="{{ $p->stock }}"
                                        data-name="{{ $p->name }}">
                                    {{ $p->name }} ({{ $p->sku }}) — Stok: {{ $p->stock }}
                                </option>
                            @endforeach
                        </select>
                        <div id="stockInfo" class="form-text mt-1"></div>
                    </div>

                    {{-- Qty --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="qty" id="qtyInput"
                               class="form-control" min="1" value="1" required>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="note" rows="2" class="form-control"
                                  placeholder="Contoh: Restock dari supplier / Barang rusak"></textarea>
                    </div>

                    {{-- Preview --}}
                    <div id="preview" class="alert alert-light border mb-0" style="display:none;">
                        <small class="fw-semibold">Preview:</small>
                        <div id="previewText" class="mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const productSelect = document.getElementById('productSelect');
    const qtyInput      = document.getElementById('qtyInput');
    const stockInfo     = document.getElementById('stockInfo');
    const preview       = document.getElementById('preview');
    const previewText   = document.getElementById('previewText');

    function updateInfo() {
        const opt = productSelect.options[productSelect.selectedIndex];
        const stock = opt.dataset.stock ? parseInt(opt.dataset.stock) : null;
        const qty = parseInt(qtyInput.value) || 0;
        const type = document.querySelector('input[name="type"]:checked').value;

        // Info stok saat ini
        if (stock !== null) {
            stockInfo.textContent = `Stok saat ini: ${stock}`;
        } else {
            stockInfo.textContent = '';
        }

        // Preview
        if (stock !== null && qty > 0) {
            let after = type === 'in' ? stock + qty : stock - qty;
            let warna = after < 0 ? 'text-danger' : 'text-success';
            preview.style.display = 'block';
            previewText.innerHTML = `
                <span class="fw-semibold">${opt.dataset.name}</span><br>
                Stok: <span class="fw-semibold">${stock}</span>
                <i class="bi bi-arrow-right"></i>
                <span class="fw-bold ${warna}">${after}</span>
                ${after < 0 ? '<br><span class="text-danger small">⚠️ Stok tidak cukup!</span>' : ''}
            `;
        } else {
            preview.style.display = 'none';
        }
    }

    productSelect.addEventListener('change', updateInfo);
    qtyInput.addEventListener('input', updateInfo);
    document.querySelectorAll('input[name="type"]').forEach(el => {
        el.addEventListener('change', updateInfo);
    });
</script>
@endpush