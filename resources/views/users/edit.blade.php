@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="mb-3">
    <h5 class="fw-bold mb-0">Edit User</h5>
    <small class="text-muted">Ubah data user <strong>{{ $user->name }}</strong></small>
</div>

<div class="card card-stat">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            @include('users._form', ['user' => $user])
        </form>
    </div>
</div>
@endsection