@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Usuarios</h1>

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">➕ Nuevo Usuario</a>

    <table class="table table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Departamento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="{{ $user->active ? '' : 'table-danger' }}">
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->name ?? '-' }}</td>
                <td>{{ $user->department->name ?? '-' }}</td>
                <td>{{ $user->active ? 'Activo' : 'Inactivo' }}</td>
                <td>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
