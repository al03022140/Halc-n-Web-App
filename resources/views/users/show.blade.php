@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Detalle de Usuario</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Rol:</strong> {{ optional($user->role)->name ?? '—' }}</p>
            <p><strong>Departamento:</strong> {{ optional($user->department)->name ?? '—' }}</p>
            <p><strong>Activo:</strong> {{ isset($user->active) ? ($user->active ? 'Sí' : 'No') : '—' }}</p>
        </div>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection