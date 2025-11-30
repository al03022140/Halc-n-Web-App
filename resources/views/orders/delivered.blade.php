@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-success">📦 Pedidos Entregados</h1>

    @if($orders->isEmpty())
        <div class="alert alert-info">No hay pedidos entregados.</div>
    @else
        <div class="table-responsive shadow-sm p-3 bg-white rounded">
            <table class="table table-striped align-middle">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Departamento</th>
                        <th>Fecha de entrega</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->department->name ?? 'N/A' }}</td>
                            <td>{{ $order->delivered_at ? $order->delivered_at->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success">Entregado</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
