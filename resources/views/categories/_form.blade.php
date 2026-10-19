<div class="mb-3">
    <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror"
           placeholder="Contoh: Minuman" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Deskripsi</label>
    <textarea name="description" rows="3"
              class="form-control @error('description') is-invalid @enderror"
              placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Simpan
    </button>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Batal
    </a>
</div>