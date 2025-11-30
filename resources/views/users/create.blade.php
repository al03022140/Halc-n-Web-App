@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Usuario</h1>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Rol</label>
            <select name="role_id" id="role_id" class="form-select">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="department_id" class="form-label">Departamento</label>
            <select name="department_id" id="department_id" class="form-select">
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">↩ Volver</a>
    </form>
</div>
@endsection
