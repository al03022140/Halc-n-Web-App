@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">🏢 Lista de Departamentos</h1>
    <a href="{{ route('departments.create') }}" class="btn btn-primary mb-3">➕ Nuevo Departamento</a>

    <table class="table table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $department)
            <tr>
                <td>{{ $department->id }}</td>
                <td>{{ $department->name }}</td>
                <td>
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                    <form action="{{ route('departments.destroy', $department) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este departamento?')">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
