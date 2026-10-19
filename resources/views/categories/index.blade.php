@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Daftar Kategori</h5>
        <small class="text-muted">Kelola kategori produk toko</small>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
    </a>
</div>

<div class="card card-stat">
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari kategori...">
                <button class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i> Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-danger">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th class="text-center">Jumlah Produk</th>
                        <th width="180" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $cat)
                    <tr>
                        <td>{{ $categories->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold">{{ $cat->name }}</div>
                            @if($cat->description)
                                <small class="text-muted">{{ Str::limit($cat->description, 50) }}</small>
                            @endif
                        </td>
                        <td><code>{{ $cat->slug }}</code></td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info-emphasis">
                                {{ $cat->products_count }} produk
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('categories.edit', $cat) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus kategori {{ $cat->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada kategori
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection