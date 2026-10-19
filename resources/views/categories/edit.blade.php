@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="mb-3">
    <h5 class="fw-bold mb-0">Edit Kategori</h5>
    <small class="text-muted">Ubah data kategori {{ $category->name }}</small>
</div>

<div class="card card-stat">
    <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf @method('PUT')
            @include('categories._form', ['category' => $category])
        </form>
    </div>
</div>
@endsection