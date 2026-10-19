@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="mb-3">
    <h5 class="fw-bold mb-0">Tambah Kategori</h5>
    <small class="text-muted">Isi form di bawah untuk menambahkan kategori baru</small>
</div>

<div class="card card-stat">
    <div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            @include('categories._form')
        </form>
    </div>
</div>
@endsection