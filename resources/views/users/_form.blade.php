<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="Contoh: Budi Santoso" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="user@kasirku.test" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Password
            @if(!isset($user)) <span class="text-danger">*</span>
            @else <small class="text-muted fw-normal">(biarkan kosong jika tidak diubah)</small> @endif
        </label>
        <div class="input-group">
            <input type="password" name="password" id="pwd"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ isset($user) ? '••••••' : 'Min 6 karakter' }}"
                   {{ isset($user) ? '' : 'required' }}>
            <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('pwd','eye1')">
                <i class="bi bi-eye" id="eye1"></i>
            </button>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Konfirmasi Password
            @if(!isset($user)) <span class="text-danger">*</span> @endif
        </label>
        <div class="input-group">
            <input type="password" name="password_confirmation" id="pwd2"
                   class="form-control"
                   placeholder="Ulangi password">
            <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('pwd2','eye2')">
                <i class="bi bi-eye" id="eye2"></i>
            </button>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Role / Hak Akses <span class="text-danger">*</span></label>
        <div class="row g-2">
            @foreach($roles as $r)
            @php
                $roleInfo = [
                    'admin'   => ['icon' => 'shield-fill-check', 'color' => 'danger',  'desc' => 'Akses penuh semua fitur'],
                    'kasir'   => ['icon' => 'cash-register',     'color' => 'success', 'desc' => 'Hanya bisa transaksi kasir'],
                    'manajer' => ['icon' => 'graph-up-arrow',    'color' => 'warning', 'desc' => 'Stok & laporan (read-only produk)'],
                ];
                $info = $roleInfo[$r->name] ?? ['icon' => 'person', 'color' => 'secondary', 'desc' => ''];
                $currentRole = old('role', isset($user) ? $user->roles->first()?->name : '');
            @endphp
            <div class="col-md-4">
                <input type="radio" class="btn-check" name="role"
                       id="role_{{ $r->id }}" value="{{ $r->name }}"
                       @checked($currentRole === $r->name) required>
                <label class="btn btn-outline-{{ $info['color'] }} w-100 text-start p-3 role-label" for="role_{{ $r->id }}">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-{{ $info['icon'] }} fs-5"></i>
                        <span class="fw-bold">{{ ucfirst($r->name) }}</span>
                    </div>
                    <small class="text-muted d-block" style="font-size:.75rem; line-height:1.3;">
                        {{ $info['desc'] }}
                    </small>
                </label>
            </div>
            @endforeach
        </div>
        @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>
</div>

<hr>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Simpan
    </button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Batal
    </a>
</div>

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
<style>
    .btn-check:checked + .role-label {
        box-shadow: 0 0 0 3px rgba(14,165,233,.25);
        transform: translateY(-2px);
    }
    .role-label { transition: all .2s; }
</style>
@endpush