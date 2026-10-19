@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="mb-3">
    <h5 class="fw-bold mb-0">Tambah Produk</h5>
    <small class="text-muted">Isi data produk dengan lengkap</small>
</div>

<div class="card card-stat">
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('products._form')
        </form>
    </div>
</div>
@endsection