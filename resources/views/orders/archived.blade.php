@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">📦 Órdenes Archivadas</h1>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mb-3">⬅ Volver a Órdenes</a>

    @if($orders->isEmpty())
        <div class="alert alert-info">No hay órdenes archivadas.</div>
    @else
        <table class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Empleado Asignado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $order->status->color ?? '#6c757d' }};">
                            {{ $order->status->name ?? 'Sin estado' }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td class="d-flex gap-2">
                            @php($currentRole = optional(Auth::user()->role)->name)
                            @if($currentRole === 'Admin' || ($currentRole === 'Sales' && optional($order->status)->name === 'Ordered'))
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            @endif
                        <form action="{{ route('orders.restore', $order->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            <button class="btn btn-success btn-sm" onclick="return confirm('¿Restaurar esta orden?')">
                                🔄 Restaurar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
