@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="mb-3">
    <h5 class="fw-bold mb-0">Tambah User Baru</h5>
    <small class="text-muted">Isi data user & pilih role yang sesuai</small>
</div>

<div class="card card-stat">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @include('users._form')
        </form>
    </div>
</div>
@endsection