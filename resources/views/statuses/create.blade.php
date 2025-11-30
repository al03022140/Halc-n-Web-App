@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Estado</h1>
    <form action="{{ route('statuses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Estado</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="color" class="form-label">Color (hexadecimal)</label>
            <input type="color" name="color" id="color" class="form-control form-control-color" required>
        </div>
        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('statuses.index') }}" class="btn btn-secondary">↩ Volver</a>
    </form>
</div>
@endsection
