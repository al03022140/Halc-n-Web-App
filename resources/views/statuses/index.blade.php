@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">📌 Lista de Estados</h1>
    <a href="{{ route('statuses.create') }}" class="btn btn-primary mb-3">➕ Nuevo Estado</a>

    <table class="table table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Color</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statuses as $status)
            <tr>
                <td>{{ $status->id }}</td>
                <td>{{ $status->name }}</td>
                <td><span class="badge" style="background-color: {{ $status->color }}">{{ $status->color }}</span></td>
                <td>
                    <a href="{{ route('statuses.edit', $status) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                    <form action="{{ route('statuses.destroy', $status) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este estado?')">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
