@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0"><i class="fas fa-users text-primary me-2"></i>Lista de Clientes</h1>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Cliente
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>ID personalizado</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td class="ps-3">{{ $client->id }}</td>
                            <td>{{ $client->custom_id ?? 'N/A' }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->phone ?? 'N/A' }}</td>
                            <td>{{ $client->email ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                        onclick="if(confirm('¿Está seguro de eliminar este cliente?')) { document.getElementById('delete-client-{{ $client->id }}').submit(); }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-client-{{ $client->id }}" action="{{ route('clients.destroy', $client) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>No hay clientes disponibles</h5>
                                    <p class="text-muted mb-2">Crea un nuevo cliente para comenzar</p>
                                    <a href="{{ route('clients.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i>Nuevo Cliente
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection