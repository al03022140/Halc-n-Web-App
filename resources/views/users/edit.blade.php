@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Usuario</h1>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Rol</label>
            <select name="role_id" id="role_id" class="form-select">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="department_id" class="form-label">Departamento</label>
            <select name="department_id" id="department_id" class="form-select">
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="active" class="form-label">Estado</label>
            <select name="active" id="active" class="form-select">
                <option value="1" {{ $user->active ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ !$user->active ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">💾 Actualizar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">↩ Volver</a>
    </form>
</div>
@endsection
