@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

<div class="mb-3">
    <h5 class="fw-bold mb-0">Profil Saya</h5>
    <small class="text-muted">Kelola informasi akun & keamanan</small>
</div>

<div class="row g-3">

    {{-- ============ KARTU KIRI: PROFIL & STATISTIK ============ --}}
    <div class="col-lg-4">
        <div class="card card-stat mb-3">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:90px; height:90px; font-size:2.25rem; font-weight:700; box-shadow: 0 8px 20px rgba(14,165,233,.3);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>

                @foreach($user->roles as $r)
                    @php
                        $colors = ['admin'=>'danger','kasir'=>'success','manajer'=>'warning'];
                        $c = $colors[$r->name] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $c }}-subtle text-{{ $c }}-emphasis fs-6 px-3 py-2">
                        <i class="bi bi-shield-check"></i> {{ ucfirst($r->name) }}
                    </span>
                @endforeach

                <hr class="my-3">

                <div class="text-start small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-calendar3"></i> Bergabung</span>
                        <span class="fw-semibold">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-receipt"></i> Transaksi</span>
                        <span class="fw-semibold">{{ number_format($stats['total_transactions']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-cash"></i> Revenue</span>
                        <span class="fw-semibold text-success">
                            Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-center small mb-0">
            <i class="bi bi-shield-lock fs-4 me-2"></i>
            <div>
                <strong>Tips Keamanan:</strong><br>
                Gunakan password yang kuat & jangan bagikan ke siapa pun.
            </div>
        </div>
    </div>

    {{-- ============ KARTU KANAN: FORM ============ --}}
    <div class="col-lg-8">
        {{-- FORM 1: Informasi Akun --}}
        <div class="card card-stat mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">
                    <i class="bi bi-person text-primary"></i> Informasi Akun
                </h6>
                <p class="text-muted small mb-4">
                    Update nama & email akun Anda.
                </p>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
                        </button>
                        @if(session('success') && !session('error'))
                            <span class="text-success small">
                                <i class="bi bi-check-circle"></i> Tersimpan
                            </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- FORM 2: Ganti Password --}}
        <div class="card card-stat">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">
                    <i class="bi bi-key text-warning"></i> Ganti Password
                </h6>
                <p class="text-muted small mb-4">
                    Pastikan password baru minimal <strong>6 karakter</strong> dan mudah Anda ingat.
                </p>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password Lama</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="pwdOld"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       placeholder="••••••" required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePwd('pwdOld','eyeOld')">
                                    <i class="bi bi-eye" id="eyeOld"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="password" id="pwdNew"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min 6 karakter" required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePwd('pwdNew','eyeNew')">
                                    <i class="bi bi-eye" id="eyeNew"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="pwdConf"
                                       class="form-control"
                                       placeholder="Ulangi password baru" required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePwd('pwdConf','eyeConf')">
                                    <i class="bi bi-eye" id="eyeConf"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning small mt-3 mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        Setelah password diubah, Anda tetap login di sesi ini.
                        Gunakan password baru untuk login berikutnya.
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-shield-lock"></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePwd(id, eyeId) {
    const pwd = document.getElementById(id);
    const icon = document.getElementById(eyeId);
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endpush