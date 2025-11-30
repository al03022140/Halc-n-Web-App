@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0"><i class="fas fa-search text-primary me-2"></i>Resultados de búsqueda</h1>
        <div>
            <a href="{{ route('orders.search') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-search me-2"></i>Nueva búsqueda
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a la lista
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($orders->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No se encontraron resultados</h4>
                <p class="text-muted mb-4">No se encontraron órdenes con ese número o criterio.</p>
                <a href="{{ route('orders.search') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Intentar otra búsqueda
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Empleado</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="ps-3">{{ $order->id }}</td>
                            <td>{{ $order->invoice_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>
                                @php($statusColor = $order->status->color ?? '#6c757d')
                                <span class="badge" style="background-color: {{ $statusColor }}; color: #fff;">
                                    {{ $order->status->name ?? 'Sin estado' }}
                                </span>
                                @if($order->has_incident)
                                    <span class="badge bg-warning text-dark ms-1">Incidencia</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>{{ $order->user->name ?? '-' }}</td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @php($currentRole = optional(Auth::user()->role)->name)
                                    @if($currentRole === 'Admin' || ($currentRole === 'Sales' && optional($order->status)->name === 'Ordered'))
                                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(optional(Auth::user()->role)->name === 'Admin')
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" 
                                                onclick="if(confirm('¿Está seguro de eliminar esta orden?')) { document.getElementById('delete-form-{{ $order->id }}').submit(); }">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $order->id }}" action="{{ route('orders.destroy', $order) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
