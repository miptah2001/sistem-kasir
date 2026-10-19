@extends('layouts.app')

@section('title', 'Kasir')

@section('content')

<div class="row g-3">

    {{-- =========== KOLOM KIRI: PRODUK =========== --}}
    <div class="col-lg-7">
        <div class="card card-stat">
            <div class="card-body">

                <div class="mb-3">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="Cari produk atau scan barcode...">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted" id="productCount">
                        {{ $products->count() }} produk tersedia
                    </small>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" onclick="filterAll(this)">Semua</button>
                    </div>
                </div>

                <div class="row g-2" id="productGrid" style="max-height: 550px; overflow-y: auto;">
                    @foreach($products as $p)
                    <div class="col-6 col-md-4 col-xl-3 product-item"
                         data-name="{{ strtolower($p->name) }}"
                         data-sku="{{ strtolower($p->sku) }}">
                        <div class="card product-card h-100 {{ $p->stock <= 5 ? 'border-danger' : '' }}"
                             style="cursor:pointer; transition: all .15s;"
                             data-id="{{ $p->id }}"
                             data-name="{{ $p->name }}"
                             data-sku="{{ $p->sku }}"
                             data-price="{{ (float) $p->price }}"
                             data-stock="{{ $p->stock }}"
                             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 16px rgba(0,0,0,.1)'"
                             onmouseout="this.style.transform='';this.style.boxShadow=''">
                            <div class="card-body p-2">
                                <div class="fw-semibold mb-1" style="font-size:.85rem; line-height:1.2; height:2.4em; overflow:hidden;">
                                    {{ $p->name }}
                                </div>
                                <div class="text-primary fw-bold" style="font-size:.9rem;">
                                    Rp {{ number_format($p->price, 0, ',', '.') }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted" style="font-size:.7rem;">{{ $p->sku }}</small>
                                    <span class="badge bg-{{ $p->stock <= 5 ? 'danger' : 'success' }}-subtle
                                                 text-{{ $p->stock <= 5 ? 'danger' : 'success' }}-emphasis"
                                          style="font-size:.65rem;">
                                        {{ $p->stock }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- =========== KOLOM KANAN: KERANJANG =========== --}}
    <div class="col-lg-5">
        <div class="card card-stat h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-cart3"></i> Keranjang</h6>
                <button class="btn btn-sm btn-outline-danger" id="clearCartBtn" style="display:none;">
                    <i class="bi bi-trash"></i> Kosongkan
                </button>
            </div>
            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                <div id="emptyCart" class="text-center text-muted py-5">
                    <i class="bi bi-cart-x" style="font-size:3rem; opacity:.3;"></i>
                    <p class="mt-2 mb-0">Keranjang masih kosong</p>
                    <small>Klik produk untuk menambahkan</small>
                </div>
                <div id="cartItems"></div>
            </div>

            <div class="card-footer bg-white border-top">
                {{-- Ringkasan --}}
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Subtotal</span>
                    <span id="subtotal" class="fw-semibold">Rp 0</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted">Diskon</span>
                    <div class="input-group input-group-sm" style="width:130px;">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="discountInput" class="form-control text-end" value="0" min="0">
                    </div>
                </div>

                <hr class="my-2">

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold fs-5">Total</span>
                    <span id="total" class="fw-bold fs-5 text-primary">Rp 0</span>
                </div>

                {{-- Metode Bayar --}}
                <div class="mb-2">
                    <label class="form-label small fw-semibold mb-1">Metode Bayar</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="pm" id="pmCash" value="cash" checked>
                        <label class="btn btn-outline-primary btn-sm" for="pmCash">
                            <i class="bi bi-cash"></i> Cash
                        </label>
                        <input type="radio" class="btn-check" name="pm" id="pmDebit" value="debit">
                        <label class="btn btn-outline-primary btn-sm" for="pmDebit">
                            <i class="bi bi-credit-card"></i> Debit
                        </label>
                        <input type="radio" class="btn-check" name="pm" id="pmQris" value="qris">
                        <label class="btn btn-outline-primary btn-sm" for="pmQris">
                            <i class="bi bi-qr-code"></i> QRIS
                        </label>
                    </div>
                </div>

                {{-- Uang Bayar --}}
                <div class="mb-2">
                    <label class="form-label small fw-semibold mb-1">Uang Bayar</label>
                    <input type="number" id="paidInput" class="form-control form-control-lg text-end fw-bold"
                           placeholder="0" min="0">
                    <div class="d-flex gap-1 mt-1 flex-wrap" id="quickAmounts">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-add="5000">+5rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-add="10000">+10rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-add="20000">+20rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-add="50000">+50rb</button>
                        <button type="button" class="btn btn-sm btn-outline-info" id="exactBtn">Uang Pas</button>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Kembalian</span>
                    <span id="change" class="fw-bold text-success">Rp 0</span>
                </div>

                <button id="checkoutBtn" class="btn btn-success btn-lg w-100" disabled>
                    <i class="bi bi-check-circle"></i> Bayar Sekarang
                </button>
            </div>
        </div>

        {{-- Transaksi hari ini --}}
        @if($todayTransactions->count())
        <div class="card card-stat mt-3">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="bi bi-clock-history"></i> Transaksi Terakhir</h6>
                @foreach($todayTransactions as $t)
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <div>
                            <a href="{{ route('pos.receipt', $t) }}" class="text-decoration-none small fw-semibold">
                                {{ $t->invoice_no }}
                            </a>
                            <div class="text-muted" style="font-size:.75rem;">
                                {{ $t->created_at->format('H:i') }} — {{ $t->total_items }} item
                            </div>
                        </div>
                        <span class="fw-bold text-primary small">{{ $t->total_formatted }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Form tersembunyi untuk submit --}}
<form id="checkoutForm" method="POST" action="{{ route('pos.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="paid" id="formPaid">
    <input type="hidden" name="discount" id="formDiscount">
    <input type="hidden" name="payment_method" id="formPaymentMethod">
    <input type="hidden" name="note" id="formNote">
    <div id="formItems"></div>
</form>

{{-- Loading overlay --}}
<div id="loadingOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="bg-white p-4 rounded text-center">
        <div class="spinner-border text-primary mb-2"></div>
        <div class="fw-semibold">Memproses transaksi...</div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .cart-row {
        padding: .6rem 0;
        border-bottom: 1px dashed #e2e8f0;
        animation: fadeIn .2s;
    }
    .cart-row:last-child { border-bottom: none; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(10px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .qty-btn {
        width: 26px; height: 26px;
        padding: 0; line-height: 1;
        border-radius: 6px;
    }
    .product-card.border-danger { border-width: 1.5px !important; }
</style>
@endpush

@push('scripts')
<script>
let cart = [];
let discount = 0;
let paymentMethod = 'cash';

const rupiah = n => 'Rp ' + (n || 0).toLocaleString('id-ID');

// ===== Render cart =====
function renderCart() {
    const container = document.getElementById('cartItems');
    const empty = document.getElementById('emptyCart');
    const clearBtn = document.getElementById('clearCartBtn');

    if (cart.length === 0) {
        container.innerHTML = '';
        empty.style.display = '';
        clearBtn.style.display = 'none';
        document.getElementById('checkoutBtn').disabled = true;
    } else {
        empty.style.display = 'none';
        clearBtn.style.display = '';
        document.getElementById('checkoutBtn').disabled = false;

        container.innerHTML = cart.map((item, i) => `
            <div class="cart-row">
                <div class="d-flex justify-content-between mb-1">
                    <div class="fw-semibold" style="font-size:.85rem;">${item.name}</div>
                    <button class="btn btn-sm text-danger p-0" onclick="removeItem(${i})">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary qty-btn" onclick="changeQty(${i}, -1)">−</button>
                        <input type="number" class="form-control form-control-sm text-center"
                               style="width:50px;" value="${item.qty}" min="1" max="${item.stock}"
                               onchange="setQty(${i}, this.value)">
                        <button class="btn btn-sm btn-outline-secondary qty-btn" onclick="changeQty(${i}, 1)">+</button>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary" style="font-size:.85rem;">${rupiah(item.price * item.qty)}</div>
                        <small class="text-muted">@ ${rupiah(item.price)}</small>
                    </div>
                </div>
            </div>
        `).join('');
    }
    updateTotals();
}

// ===== Update totals =====
function updateTotals() {
    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    discount = Math.min(parseFloat(document.getElementById('discountInput').value) || 0, subtotal);
    const total = subtotal - discount;

    document.getElementById('subtotal').textContent = rupiah(subtotal);
    document.getElementById('total').textContent = rupiah(total);

    // Kembalian
    const paid = parseFloat(document.getElementById('paidInput').value) || 0;
    const change = paid - total;
    const changeEl = document.getElementById('change');
    changeEl.textContent = rupiah(change);
    changeEl.className = 'fw-bold ' + (change < 0 ? 'text-danger' : 'text-success');

    // Enable checkout jika cukup
    const btn = document.getElementById('checkoutBtn');
    const isValid = cart.length > 0 && (paymentMethod !== 'cash' || paid >= total);
    btn.disabled = !isValid;

    if (cart.length > 0 && paymentMethod === 'cash' && paid < total) {
        btn.innerHTML = '<i class="bi bi-exclamation-circle"></i> Uang kurang';
    } else if (cart.length > 0) {
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Bayar Sekarang';
    }
}

// ===== Add to cart =====
function addToCart(card) {
    const id = card.dataset.id;
    const existing = cart.find(i => i.id === id);
    const stock = parseInt(card.dataset.stock);

    if (existing) {
        if (existing.qty >= stock) {
            notify('Stok tidak cukup', 'danger');
            return;
        }
        existing.qty++;
    } else {
        cart.push({
            id,
            name: card.dataset.name,
            sku: card.dataset.sku,
            price: parseFloat(card.dataset.price),
            stock: stock,
            qty: 1,
        });
    }
    renderCart();
}

// ===== Cart actions =====
function changeQty(i, delta) {
    const item = cart[i];
    const newQty = item.qty + delta;
    if (newQty < 1) { removeItem(i); return; }
    if (newQty > item.stock) { notify('Stok tidak cukup', 'danger'); return; }
    item.qty = newQty;
    renderCart();
}

function setQty(i, val) {
    const qty = parseInt(val) || 1;
    if (qty > cart[i].stock) { notify('Stok tidak cukup', 'danger'); cart[i].qty = cart[i].stock; }
    else if (qty < 1) cart[i].qty = 1;
    else cart[i].qty = qty;
    renderCart();
}

function removeItem(i) { cart.splice(i, 1); renderCart(); }

document.getElementById('clearCartBtn').addEventListener('click', () => {
    if (confirm('Kosongkan keranjang?')) { cart = []; renderCart(); }
});

// ===== Click produk =====
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', () => addToCart(card));
});

// ===== Search =====
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.product-item').forEach(el => {
        const match = el.dataset.name.includes(q) || el.dataset.sku.includes(q);
        el.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('productCount').textContent = `${visible} produk ditemukan`;
});

// ===== Diskon =====
document.getElementById('discountInput').addEventListener('input', updateTotals);

// ===== Payment method =====
document.querySelectorAll('input[name="pm"]').forEach(el => {
    el.addEventListener('change', function () {
        paymentMethod = this.value;
        const paidInput = document.getElementById('paidInput');
        const quickAmounts = document.getElementById('quickAmounts');

        if (paymentMethod !== 'cash') {
            paidInput.value = cart.reduce((s, i) => s + i.price * i.qty, 0) - discount;
            paidInput.readOnly = true;
            quickAmounts.style.display = 'none';
        } else {
            paidInput.readOnly = false;
            quickAmounts.style.display = 'flex';
        }
        updateTotals();
    });
});

// ===== Uang bayar =====
document.getElementById('paidInput').addEventListener('input', updateTotals);

document.querySelectorAll('#quickAmounts [data-add]').forEach(btn => {
    btn.addEventListener('click', () => {
        const current = parseFloat(document.getElementById('paidInput').value) || 0;
        document.getElementById('paidInput').value = current + parseInt(btn.dataset.add);
        updateTotals();
    });
});

document.getElementById('exactBtn').addEventListener('click', () => {
    const total = cart.reduce((s, i) => s + i.price * i.qty, 0) - discount;
    document.getElementById('paidInput').value = total;
    updateTotals();
});

// ===== Checkout =====
document.getElementById('checkoutBtn').addEventListener('click', () => {
    if (cart.length === 0) return;

    const total = cart.reduce((s, i) => s + i.price * i.qty, 0) - discount;
    const paid = parseFloat(document.getElementById('paidInput').value) || 0;

    if (paymentMethod === 'cash' && paid < total) {
        notify('Uang bayar kurang', 'danger');
        return;
    }

    if (!confirm('Proses transaksi sekarang?')) return;

    // Isi form
    document.getElementById('formPaid').value = paid;
    document.getElementById('formDiscount').value = discount;
    document.getElementById('formPaymentMethod').value = paymentMethod;

    document.getElementById('formItems').innerHTML = cart.map((item, i) => `
        <input type="hidden" name="items[${i}][id]" value="${item.id}">
        <input type="hidden" name="items[${i}][qty]" value="${item.qty}">
    `).join('');

    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('checkoutForm').submit();
});

// ===== Notify =====
function notify(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `alert alert-${type} position-fixed`;
    el.style.cssText = 'top:20px; right:20px; z-index:99999; min-width:250px; box-shadow:0 4px 12px rgba(0,0,0,.15);';
    el.innerHTML = `<i class="bi bi-info-circle"></i> ${msg}`;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 2000);
}

// ===== Keyboard shortcut =====
document.addEventListener('keydown', function (e) {
    // F2 = focus search
    if (e.key === 'F2') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    // F4 = focus paid
    if (e.key === 'F4') {
        e.preventDefault();
        document.getElementById('paidInput').focus();
    }
    // Enter di search = tambah produk pertama yang match
    if (e.key === 'Enter' && document.activeElement.id === 'searchInput') {
        e.preventDefault();
        const first = document.querySelector('.product-item:not([style*="display: none"]) .product-card');
        if (first) { addToCart(first); document.getElementById('searchInput').value = ''; }
    }
});
</script>
@endpush