@extends('layouts.app')

@section('title', 'Detail User')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Detail User</h5>
        <small class="text-muted">{{ $user->name }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Kartu Profil --}}
    <div class="col-md-4">
        <div class="card card-stat">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:80px; height:80px; font-size:2rem; font-weight:700;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3 small">{{ $user->email }}</p>

                @foreach($user->roles as $r)
                    @php
                        $colors = ['admin'=>'danger','kasir'=>'success','manajer'=>'warning'];
                        $c = $colors[$r->name] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }}-emphasis fs-6">
                        <i class="bi bi-shield-check"></i> {{ ucfirst($r->name) }}
                    </span>
                @endforeach

                <hr>
                <div class="text-start small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Bergabung</span>
                        <span class="fw-semibold">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-success-subtle text-success-emphasis">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Aktivitas --}}
    <div class="col-md-8">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card card-stat h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary-subtle p-3 d-inline-flex mb-2">
                            <i class="bi bi-receipt text-primary fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ number_format($stats['total_transactions']) }}</h3>
                        <small class="text-muted">Total Transaksi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-success-subtle p-3 d-inline-flex mb-2">
                            <i class="bi bi-cash-coin text-success fs-3"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-success">
                            Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                        </h5>
                        <small class="text-muted">Total Revenue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-warning-subtle p-3 d-inline-flex mb-2">
                            <i class="bi bi-arrow-left-right text-warning fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ number_format($stats['total_stock_moves']) }}</h3>
                        <small class="text-muted">Aktivitas Stok</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Detail --}}
        <div class="card card-stat">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Informasi Akun</h6>
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="180" class="text-muted">Nama Lengkap</th>
                        <td class="fw-semibold">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Role</th>
                        <td>
                            @foreach($user->roles as $r)
                                <span class="badge bg-primary">{{ ucfirst($r->name) }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Dibuat</th>
                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Terakhir Update</th>
                        <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection