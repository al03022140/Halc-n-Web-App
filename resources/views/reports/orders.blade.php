@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Reporte de pedidos</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Panel</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.orders') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="status_id" class="form-label">Estado</label>
                <select class="form-select" id="status_id" name="status_id">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ ($filters['status_id'] ?? '') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="client_id" class="form-label">Cliente</label>
                <select class="form-select" id="client_id" name="client_id">
                    <option value="">Todos</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ ($filters['client_id'] ?? '') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Desde</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Hasta</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-2"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Totales por estado</h5>
            </div>
            <div class="card-body">
                @forelse($totalsByStatus as $status => $count)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $status }}</span>
                        <strong>{{ $count }}</strong>
                    </div>
                @empty
                    <p class="text-muted">No hay resultados para los filtros seleccionados.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pedidos</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Dirección</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->invoice_number }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ optional($order->status)->name ?? 'Sin estado' }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($order->delivery_address, 40) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Sin pedidos para el criterio seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
