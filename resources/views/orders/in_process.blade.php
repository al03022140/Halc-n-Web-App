@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-warning">🚚 Pedidos en Proceso</h1>

    @if($orders->isEmpty())
        <div class="alert alert-info">No hay pedidos en proceso.</div>
    @else
        <div class="table-responsive shadow-sm p-3 bg-white rounded">
            <table class="table table-striped align-middle">
                <thead class="table-warning">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Departamento</th>
                        <th>Fecha estimada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->department->name ?? 'N/A' }}</td>
                            <td>{{ $order->estimated_date ? $order->estimated_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">En proceso</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
