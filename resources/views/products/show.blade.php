@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0"><i class="fas fa-box text-info me-2"></i>Detalles del Producto</h1>
        <div>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a la lista
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre:</label>
                    <p class="form-control-plaintext">{{ $product->name }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">SKU:</label>
                    <p class="form-control-plaintext">{{ $product->sku ?? 'No especificado' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Precio:</label>
                    <p class="form-control-plaintext">${{ number_format($product->price, 2) }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Stock:</label>
                    <p class="form-control-plaintext">{{ $product->stock ?? 0 }} unidades</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Estado:</label>
                    <p class="form-control-plaintext">
                        @if($product->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Fecha de Creación:</label>
                    <p class="form-control-plaintext">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold">Descripción:</label>
            <p class="form-control-plaintext">{{ $product->description ?? 'No especificada' }}</p>
        </div>

        @if($product->orders->count() > 0)
        <hr>
        <h5><i class="fas fa-shopping-cart me-2"></i>Órdenes con este Producto</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Factura</th>
                        <th>Cliente</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->invoice_number }}</td>
                        <td>{{ $order->client->name ?? 'N/A' }}</td>
                        <td>{{ $order->quantity ?? 1 }}</td>
                        <td>{{ $order->order_date }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status->color ?? 'secondary' }}">
                                {{ $order->status->name ?? 'Sin estado' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
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