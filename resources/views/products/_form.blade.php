<div class="row g-3">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                       class="form-control @error('sku') is-invalid @enderror"
                       placeholder="Contoh: PRD-001" required>
                @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Harga Modal <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}"
                           class="form-control @error('cost_price') is-invalid @enderror" min="0" required>
                    @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? 0) }}"
                           class="form-control @error('price') is-invalid @enderror" min="0" required>
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
                       class="form-control @error('stock') is-invalid @enderror" min="0" required>
                @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked(old('is_active', $product->is_active ?? true))>
            <label class="form-check-label" for="is_active">Produk aktif (bisa dijual)</label>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Gambar Produk</label>
        <div class="border rounded d-flex align-items-center justify-content-center mb-2"
             style="height:220px; background:#f8fafc; overflow:hidden;">
            <img id="preview" src="{{ isset($product) && $product->image ? asset('storage/'.$product->image) : '' }}"
                 style="max-width:100%; max-height:100%; {{ isset($product) && $product->image ? '' : 'display:none;' }}">
            <i id="placeholder-icon" class="bi bi-image text-muted" style="font-size:3rem; {{ isset($product) && $product->image ? 'display:none;' : '' }}"></i>
        </div>
        <input type="file" name="image" id="imageInput" accept="image/*"
               class="form-control @error('image') is-invalid @enderror">
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">JPG/PNG/WebP, max 2MB</small>
    </div>
</div>

<hr>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Simpan
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Batal
    </a>
</div>

@push('scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const preview = document.getElementById('preview');
                preview.src = ev.target.result;
                preview.style.display = 'block';
                document.getElementById('placeholder-icon').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush