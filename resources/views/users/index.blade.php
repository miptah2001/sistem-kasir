@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Manajemen User</h5>
        <small class="text-muted">Kelola akun & role pengguna</small>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Tambah User
    </a>
</div>

{{-- ===== FILTER ===== --}}
<div class="card card-stat mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama atau email...">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" @selected(request('role') === $r->name)>
                            {{ ucfirst($r->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- ===== TABEL ===== --}}
<div class="card card-stat">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th width="130">Role</th>
                        <th width="130" class="text-center">Bergabung</th>
                        <th width="180" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width:36px;height:36px;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info-subtle text-info-emphasis ms-1"
                                                  style="font-size:.65rem;">Anda</span>
                                        @endif
                                    </div>
                                    @if($user->roles->count())
                                        <small class="text-muted">{{ $user->roles->count() }} role</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><small>{{ $user->email }}</small></td>
                        <td>
                            @forelse($user->roles as $r)
                                @php
                                    $roleColors = [
                                        'admin'   => 'danger',
                                        'kasir'   => 'success',
                                        'manajer' => 'warning',
                                    ];
                                    $color = $roleColors[$r->name] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis">
                                    <i class="bi bi-shield-check"></i> {{ ucfirst($r->name) }}
                                </span>
                            @empty
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    No role
                                </span>
                            @endforelse
                        </td>
                        <td class="text-center">
                            <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                            Belum ada user
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $users->links() }}</div>

@endsection