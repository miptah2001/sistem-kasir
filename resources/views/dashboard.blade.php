@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* === WELCOME BANNER (GLASSMORPHISM) === */
    .welcome-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        filter: blur(20px);
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -30%; left: 20%;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        filter: blur(20px);
    }
    .welcome-banner .content { position: relative; z-index: 2; }

    /* === STAT CARDS === */
    .stat-card-modern {
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.03);
        background: #fff;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .stat-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .stat-value {
        font-size: 1.8rem; font-weight: 800;
        color: #1e293b; line-height: 1.2;
    }
    .stat-label {
        font-size: 0.85rem; font-weight: 600;
        color: #64748b; text-transform: uppercase;
        letter-spacing: 0.5px; margin-bottom: 0.25rem;
    }

    /* === CHART CONTAINER === */
    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }

    /* === ACTIVITY LIST === */
    .activity-list { list-style: none; padding: 0; margin: 0; }
    .activity-item {
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .activity-item:last-child { border-bottom: none; }
    
    .avatar-sm {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 1rem;
    }

    /* === MODERN CARD === */
    .card-modern {
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.03);
        background: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        height: 100%;
    }
    .card-modern .card-header {
        background: transparent;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }
</style>
@endpush

@section('content')

{{-- 1. WELCOME BANNER --}}
<div class="welcome-banner mb-4">
    <div class="row align-items-center content">
        <div class="col-md-8">
            <h2 class="fw-bold mb-2">Halo, {{ auth()->user()->name }}! </h2>
            <p class="mb-0 fs-5 opacity-75">
                @if($stats['trx_today'] > 0)
                    Hari ini kamu sudah memproses <strong>{{ $stats['trx_today'] }} transaksi</strong> dengan total <strong>Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</strong>. Kerja bagus!
                @else
                    Belum ada transaksi hari ini. Yuk mulai jualan dan raih revenue pertamamu hari ini!
                @endif
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('pos.index') }}" class="btn btn-light btn-lg fw-bold text-primary shadow-sm px-4">
                <i class="bi bi-cart-plus"></i> Mulai Kasir
            </a>
        </div>
    </div>
</div>

{{-- 2. STATISTIK KARTU --}}
<div class="row g-4 mb-4">
    {{-- Total Produk --}}
    <div class="col-md-6 col-xl-3">
        <div class="stat-card-modern">
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-label">Total Produk</div>
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="text-muted small mt-2">
                <i class="bi bi-check-circle-fill text-success"></i> SKU Aktif
            </div>
        </div>
    </div>

    {{-- Transaksi Hari Ini --}}
    <div class="col-md-6 col-xl-3">
        <div class="stat-card-modern">
            <div class="stat-icon bg-success-subtle text-success">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div class="stat-label">Transaksi Hari Ini</div>
            <div class="stat-value">{{ number_format($stats['trx_today']) }}</div>
            <div class="text-muted small mt-2">
                <i class="bi bi-clock-history"></i> Real-time update
            </div>
        </div>
    </div>

    {{-- Pendapatan --}}
    <div class="col-md-6 col-xl-3">
        <div class="stat-card-modern">
            <div class="stat-icon bg-info-subtle text-info">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value text-primary">
                Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}
            </div>
            <div class="mt-2 small">
                @if($stats['revenue_growth'] > 0)
                    <span class="badge bg-success-subtle text-success-emphasis">
                        <i class="bi bi-arrow-up-right"></i> {{ number_format($stats['revenue_growth'], 1) }}% vs Kemarin
                    </span>
                @elseif($stats['revenue_growth'] < 0)
                    <span class="badge bg-danger-subtle text-danger-emphasis">
                        <i class="bi bi-arrow-down-right"></i> {{ number_format(abs($stats['revenue_growth']), 1) }}% vs Kemarin
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Sama dengan kemarin</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Peringatan Stok --}}
    <div class="col-md-6 col-xl-3">
        <div class="stat-card-modern">
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-label">Butuh Restock</div>
            <div class="stat-value text-warning">{{ number_format($stats['low_stock']) }}</div>
            <div class="text-muted small mt-2">
                @if($stats['out_of_stock'] > 0)
                    <span class="text-danger fw-semibold">{{ $stats['out_of_stock'] }} produk habis!</span>
                @else
                    Stok menipis (≤ 5)
                @endif
            </div>
        </div>
    </div>
</div>

{{-- 3. GRAFIK & TOP PRODUK --}}
<div class="row g-4 mb-4">
    {{-- Grafik --}}
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up text-primary"></i> Tren Penjualan</h6>
                <span class="badge bg-light text-dark border">7 Hari Terakhir</span>
            </div>
            <div class="card-body p-4">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Produk --}}
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-trophy text-warning"></i> Top Produk</h6>
                <a href="{{ route('reports.sales') }}" class="small text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($topProducts as $i => $p)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="fw-bold me-3 text-muted" style="font-size: 1.2rem; width: 20px;">{{ $i+1 }}</div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate">{{ $p->product_name }}</div>
                        <div class="small text-muted">{{ number_format($p->total_qty) }} terjual</div>
                    </div>
                    <div class="fw-bold text-success text-end" style="font-size: 0.85rem;">
                        Rp {{ number_format($p->total_revenue/1000, 0, ',', '.') }}k
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 opacity-25"></i>
                    <p class="mt-2 mb-0 small">Belum ada penjualan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- 4. TRANSAKSI & STOK RENDAH --}}
<div class="row g-4">
    {{-- Transaksi Terbaru --}}
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary"></i> Transaksi Terbaru</h6>
            </div>
            <div class="card-body p-3">
                <ul class="activity-list">
                    @forelse($recentTransactions as $t)
                    <li class="activity-item">
                        <div class="avatar-sm bg-primary-subtle text-primary">
                            {{ strtoupper(substr($t->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $t->invoice_no }}</div>
                            <div class="small text-muted">{{ $t->user->name }} • {{ $t->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                            <span class="badge bg-success-subtle text-success-emphasis" style="font-size: 0.65rem;">
                                {{ $t->total_items }} item
                            </span>
                        </div>
                    </li>
                    @empty
                    <div class="text-center py-4 text-muted">Belum ada transaksi</div>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Stok Rendah --}}
    <div class="col-lg-5">
        <div class="card-modern">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-circle text-danger"></i> Segera Restock</h6>
            </div>
            <div class="card-body p-3">
                <ul class="activity-list">
                    @forelse($lowStockProducts as $p)
                    <li class="activity-item">
                        <div class="avatar-sm bg-danger-subtle text-danger">
                            <i class="bi bi-box"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">{{ $p->name }}</div>
                            <div class="small text-muted"><code>{{ $p->sku }}</code></div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-danger">{{ $p->stock }} Sisa</span>
                        </div>
                    </li>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle text-success fs-2 mb-2 d-block"></i>
                        Semua stok aman!
                    </div>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartData = @json($chartData);
    const labels = chartData.map(d => d.label);
    const revenues = chartData.map(d => d.revenue);

    const ctx = document.getElementById('salesChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(14, 165, 233, 0.5)');
    gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: revenues,
                borderColor: '#0ea5e9',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0ea5e9',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    callbacks: {
                        label: (context) => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5], color: '#e2e8f0' },
                    ticks: {
                        callback: (val) => 'Rp ' + (val/1000) + 'k'
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush