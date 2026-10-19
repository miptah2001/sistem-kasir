@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="mb-3">
    <h5 class="fw-bold mb-0">Edit Produk</h5>
    <small class="text-muted">Ubah data produk {{ $product->name }}</small>
</div>

<div class="card card-stat">
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('products._form', ['product' => $product])
        </form>
    </div>
</div>
@endsection