@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Resultados de Búsqueda</h3>
                    <a href="{{ route('home') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-search"></i> Nueva Búsqueda
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h5>Resultados para: "{{ $query }}"</h5>
                    </div>

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Factura</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->invoice_number }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ $order->order_date ? date('d/m/Y', strtotime($order->order_date)) : date('d/m/Y', strtotime($order->created_at)) }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($order->status->name == 'Ordered') bg-info
                                                @elseif($order->status->name == 'In process') bg-warning
                                                @elseif($order->status->name == 'In route') bg-primary
                                                @elseif($order->status->name == 'Delivered') bg-success
                                                @else bg-secondary @endif">
                                                {{ $order->status->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('track.view', ['customer_custom_id' => $order->customer_custom_id ?? '', 'invoice_number' => $order->invoice_number]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No se encontraron pedidos que coincidan con tu búsqueda.
                        </div>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection