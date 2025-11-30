@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Rol</h1>
    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Rol</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">💾 Actualizar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">↩ Volver</a>
    </form>
</div>
@endsection
