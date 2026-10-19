@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="card auth-card">
    <div class="card-body p-4 p-md-5">
        <div class="brand-logo"></div>
        <h4 class="text-center fw-bold mb-1">Sistem Kasir</h4>
        <p class="text-center text-muted mb-4">Silakan login untuk melanjutkan</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="admin@kasirku.test" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control" placeholder="••••••••" required>
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePassword()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>

        <hr class="my-4">
        <div class="text-center small text-muted">
            
            
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection