<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — KasirKu</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}
        :root {
    /* Konten Utama */
    --primary: #0ea5e9;
    --primary-dark: #0284c7;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    
    /* SIDEBAR THEME (Deep Space Navy) */
    --sidebar-bg: #0f172a;       /* Biru sangat gelap */
    --sidebar-hover: #1e293b;    /* Hover state */
    --sidebar-text: #94a3b8;     /* Teks tidak aktif */
    --sidebar-text-hover: #ffffff; /* Teks saat hover/aktif */
}

/* === SIDEBAR STYLING === */
.sidebar {
    width: 250px;
    min-height: 100vh;
    background: var(--sidebar-bg);
    position: fixed;
    top: 0; left: 0;
    padding: 1rem 0;
    overflow-y: auto;
    z-index: 1000;
}

.sidebar-brand {
    color: #fff;
    font-size: 1.25rem;
    font-weight: 700;
    padding: 0.5rem 1.25rem 1rem;
    border-bottom: 1px solid #1e293b;
    margin-bottom: 0.5rem;
    letter-spacing: -0.5px;
}

.sidebar-menu { list-style: none; padding: 0; margin: 0; }

.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--sidebar-text);
    padding: 0.7rem 1.25rem;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}

/* Efek Hover */
.sidebar-menu a:hover {
    background: var(--sidebar-hover);
    color: var(--sidebar-text-hover);
}

/* Menu Aktif dengan Gradient Border/Aksen */
.sidebar-menu a.active {
    background: linear-gradient(90deg, rgba(14, 165, 233, 0.15) 0%, transparent 100%);
    color: var(--sidebar-text-hover);
    border-left-color: var(--primary);
}

.sidebar-menu a i { 
    font-size: 1.1rem; 
    transition: transform 0.2s;
}

/* Icon sedikit membesar saat hover */
.sidebar-menu a:hover i {
    transform: scale(1.1);
}

        /* Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: 0.85rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .content-wrapper { padding: 1.5rem; }

        /* Card */
        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            transition: transform 0.2s;
        }
        .card-stat:hover { transform: translateY(-3px); }

        /* Table */
        .table-modern th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-modern td { vertical-align: middle; }

        /* Button */
        .btn { border-radius: 8px; font-weight: 500; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
    </style>
    @stack('styles')
</head>
<body>

@auth
<aside class="sidebar">
    <div class="sidebar-brand">Sistem Kasir</div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        
        @can('manage-categories')
        <li>
            <a href="{{ route('categories.index') }}">
                <i class="bi bi-tags"></i> Kategori
            </a>
        </li>
        @endcan

        @can('manage-products')
        <li>
            <a href="{{ route('products.index') }}">
                <i class="bi bi-box-seam"></i> Produk
            </a>
        </li>
        @endcan

        @can('manage-stock')
        <li>
            <a href="{{ route('stocks.index') }}">
                <i class="bi bi-arrow-left-right"></i> Stok
            </a>
        </li>
        @endcan

        @can('create-transaction')
        <li>
            <a href="{{ route('pos.index') }}">
                <i class="bi bi-cart-check"></i> Kasir
            </a>
        </li>
        @endcan
{{-- Comment dulu sampai modulnya dibuat --}}
        
        @can('view-reports')
<li>
    <a href="{{ route('reports.sales') }}" class="{{ request()->routeIs('reports.sales*') ? 'active' : '' }}">
        <i class="bi bi-graph-up-arrow"></i> Laporan Penjualan
    </a>
</li>
<li>
    <a href="{{ route('reports.stock') }}" class="{{ request()->routeIs('reports.stock*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-data"></i> Laporan Stok
    </a>
</li>
@endcan
 @can('manage-users')
<li>
    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Manajemen User
    </a>
</li>
@endcan
    </ul>
</aside>
{{-- ... --}}

<div class="main-content">
    <div class="topbar">
        <h5 class="mb-0 fw-semibold">@yield('title', 'Dashboard')</h5>
        <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown">
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
             style="width:36px;height:36px;">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div>
            <div class="fw-semibold" style="font-size:.9rem;">{{ auth()->user()->name }}</div>
            <div class="text-muted" style="font-size:.75rem;">
                {{ auth()->user()->getRoleNames()->first() ?? 'user' }}
            </div>
        </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li>
            <h6 class="dropdown-header">
                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
            </h6>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-gear"></i> Profil Saya
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
    </div>

    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>
@else
    @yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>