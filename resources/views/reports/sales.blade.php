@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        transition: transform .2s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 6rem;
        opacity: .08;
    }
    .chart-container {
        position: relative;
        height: 320px;
    }
    .rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: .8rem;
    }
    .rank-1 { background: #fbbf24; color: #fff; }
    .rank-2 { background: #94a3b8; color: #fff; }
    .rank-3 { background: #d97706; color: #fff; }
    .rank-other { background: #e2e8f0; color: #64748b; }

    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .sidebar, .topbar { display: none !important; }
        .main-content { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@endpush

@section('content')

{{-- ============ HEADER ============ --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 no-print">
    <div>
        <h5 class="mb-0 fw-bold">Laporan Penjualan</h5>
        <small class="text-muted">Periode {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.sales.export', ['from' => $from, 'to' => $to]) }}"
           class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i> Print
        </button>
    </div>
</div>

{{-- ============ FILTER ============ --}}
<div class="card card-stat mb-3 no-print">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-md-6 d-flex gap-2 flex-wrap">
                <button class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Terapkan
                </button>
                <button type="button" onclick="setPeriod('today')" class="btn btn-outline-secondary btn-sm">Hari Ini</button>
                <button type="button" onclick="setPeriod('week')" class="btn btn-outline-secondary btn-sm">7 Hari</button>
                <button type="button" onclick="setPeriod('month')" class="btn btn-outline-secondary btn-sm">Bulan Ini</button>
                <button type="button" onclick="setPeriod('year')" class="btn btn-outline-secondary btn-sm">Tahun Ini</button>
            </div>
        </form>
    </div>
</div>

{{-- ============ STATISTIK ============ --}}
<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff;">
            <div class="card-body position-relative">
                <i class="bi bi-receipt-cutoff icon-bg"></i>
                <small class="fw-semibold opacity-75">Total Transaksi</small>
                <h3 class="fw-bold mb-0 mt-2">{{ number_format($summary['total_transactions']) }}</h3>
                <small class="opacity-75">selama periode</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff;">
            <div class="card-body position-relative">
                <i class="bi bi-cash-coin icon-bg"></i>
                <small class="fw-semibold opacity-75">Total Pendapatan</small>
                <h3 class="fw-bold mb-0 mt-2">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
                <small class="opacity-75">dari semua transaksi</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff;">
            <div class="card-body position-relative">
                <i class="bi bi-box-seam icon-bg"></i>
                <small class="fw-semibold opacity-75">Total Item Terjual</small>
                <h3 class="fw-bold mb-0 mt-2">{{ number_format($summary['total_items_sold']) }}</h3>
                <small class="opacity-75">unit produk</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff;">
            <div class="card-body position-relative">
                <i class="bi bi-graph-up-arrow icon-bg"></i>
                <small class="fw-semibold opacity-75">Rata-rata / Transaksi</small>
                <h3 class="fw-bold mb-0 mt-2">Rp {{ number_format($summary['avg_per_transaction'], 0, ',', '.') }}</h3>
                <small class="opacity-75">nilai rata-rata</small>
            </div>
        </div>
    </div>
</div>

{{-- ============ GRAFIK PENJUALAN ============ --}}
<div class="card card-stat mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-line"></i> Grafik Penjualan Harian</h6>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary active" onclick="switchChart('revenue', this)">Pendapatan</button>
                <button type="button" class="btn btn-outline-primary" onclick="switchChart('count', this)">Transaksi</button>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">

    {{-- ============ TOP 10 PRODUK ============ --}}
    <div class="col-lg-7">
        <div class="card card-stat h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-trophy"></i> Top 10 Produk Terlaris</h6>
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Produk</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-end">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $i => $p)
                            <tr>
                                <td>
                                    <span class="rank-badge {{ $i < 3 ? 'rank-'.($i+1) : 'rank-other' }}">
                                        {{ $i + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold" style="font-size:.9rem;">{{ $p->product_name }}</div>
                                    <small class="text-muted"><code>{{ $p->product_sku }}</code></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary-emphasis">
                                        {{ number_format($p->total_qty) }} unit
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($p->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada penjualan di periode ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ PER KASIR + METODE BAYAR ============ --}}
    <div class="col-lg-5">
        {{-- Per Kasir --}}
        <div class="card card-stat mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-people"></i> Performa Kasir</h6>
                @forelse($byCashier as $c)
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                         style="width:36px; height:36px; font-weight:bold;">
                        {{ strtoupper(substr($c->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold" style="font-size:.9rem;">{{ $c->user->name }}</span>
                            <span class="fw-bold text-success" style="font-size:.9rem;">
                                Rp {{ number_format($c->revenue, 0, ',', '.') }}
                            </span>
                        </div>
                        <small class="text-muted">{{ $c->trx_count }} transaksi</small>
                    </div>
                </div>
                @empty
                <div class="text-muted text-center py-3">Belum ada data</div>
                @endforelse
            </div>
        </div>

        {{-- Metode Bayar --}}
        <div class="card card-stat">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-credit-card"></i> Metode Pembayaran</h6>
                @php
                    $paymentIcons = ['cash' => 'cash', 'debit' => 'credit-card', 'qris' => 'qr-code'];
                    $paymentColors = ['cash' => 'success', 'debit' => 'primary', 'qris' => 'info'];
                @endphp
                @forelse($byPayment as $p)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded"
                     style="background:#f8fafc;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-{{ $paymentIcons[$p->payment_method] ?? 'cash' }} text-{{ $paymentColors[$p->payment_method] ?? 'secondary' }} me-2 fs-5"></i>
                        <div>
                            <div class="fw-semibold" style="font-size:.85rem;">{{ strtoupper($p->payment_method) }}</div>
                            <small class="text-muted">{{ $p->count }} transaksi</small>
                        </div>
                    </div>
                    <span class="fw-bold">Rp {{ number_format($p->revenue, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="text-muted text-center py-3">Belum ada data</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ============ TABEL TRANSAKSI ============ --}}
<div class="card card-stat">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-list-ul"></i> Detail Transaksi</h6>
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th class="text-center">Item</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Metode</th>
                        <th width="80"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td><code class="fw-semibold">{{ $t->invoice_no }}</code></td>
                        <td>
                            <div style="font-size:.85rem;">{{ $t->created_at->format('d M Y') }}</div>
                            <small class="text-muted">{{ $t->created_at->format('H:i') }}</small>
                        </td>
                        <td>{{ $t->user->name }}</td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info-emphasis">
                                {{ $t->total_items }} item
                            </span>
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $paymentColors[$t->payment_method] ?? 'secondary' }}-subtle
                                         text-{{ $paymentColors[$t->payment_method] ?? 'secondary' }}-emphasis">
                                {{ strtoupper($t->payment_method) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('pos.receipt', $t) }}" target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 no-print">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ===== Data dari backend =====
    const dailyData = @json($dailySales);

    const labels   = dailyData.map(d => {
        const dt = new Date(d.date);
        return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    });
    const revenues = dailyData.map(d => parseFloat(d.revenue));
    const counts   = dailyData.map(d => parseInt(d.count));

    // ===== Setup chart =====
    const ctx = document.getElementById('salesChart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(14, 165, 233, 0.4)');
    gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

    let currentMode = 'revenue';

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: revenues,
                borderColor: '#0ea5e9',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0ea5e9',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function (context) {
                            const val = context.parsed.y;
                            if (currentMode === 'revenue') {
                                return 'Pendapatan: Rp ' + val.toLocaleString('id-ID');
                            }
                            return 'Transaksi: ' + val;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        callback: function (value) {
                            if (currentMode === 'revenue') {
                                if (value >= 1_000_000) return 'Rp ' + (value/1_000_000).toFixed(1) + 'jt';
                                if (value >= 1_000) return 'Rp ' + (value/1_000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            }
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // ===== Switch mode =====
    function switchChart(mode, btn) {
        currentMode = mode;
        document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        if (mode === 'revenue') {
            chart.data.datasets[0].data = revenues;
            chart.data.datasets[0].label = 'Pendapatan (Rp)';
            chart.data.datasets[0].borderColor = '#0ea5e9';
            chart.data.datasets[0].pointBackgroundColor = '#0ea5e9';

            const g = ctx.createLinearGradient(0, 0, 0, 320);
            g.addColorStop(0, 'rgba(14, 165, 233, 0.4)');
            g.addColorStop(1, 'rgba(14, 165, 233, 0)');
            chart.data.datasets[0].backgroundColor = g;
        } else {
            chart.data.datasets[0].data = counts;
            chart.data.datasets[0].label = 'Jumlah Transaksi';
            chart.data.datasets[0].borderColor = '#10b981';
            chart.data.datasets[0].pointBackgroundColor = '#10b981';

            const g = ctx.createLinearGradient(0, 0, 0, 320);
            g.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            g.addColorStop(1, 'rgba(16, 185, 129, 0)');
            chart.data.datasets[0].backgroundColor = g;
        }
        chart.update();
    }

    // ===== Quick period =====
    function setPeriod(period) {
        const today = new Date();
        let from, to = today.toISOString().split('T')[0];

        if (period === 'today') {
            from = to;
        } else if (period === 'week') {
            const d = new Date(today);
            d.setDate(d.getDate() - 6);
            from = d.toISOString().split('T')[0];
        } else if (period === 'month') {
            from = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
        } else if (period === 'year') {
            from = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
        }

        window.location.href = `{{ route('reports.sales') }}?from=${from}&to=${to}`;
    }
</script>
@endpush