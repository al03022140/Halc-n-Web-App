@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Departamento</h1>
    <form action="{{ route('departments.update', $department) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Departamento</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $department->name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">💾 Actualizar</button>
        <a href="{{ route('departments.index') }}" class="btn btn-secondary">↩ Volver</a>
    </form>
</div>
@endsection
