@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Rol</h1>
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Rol</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">↩ Volver</a>
    </form>
</div>
@endsection
