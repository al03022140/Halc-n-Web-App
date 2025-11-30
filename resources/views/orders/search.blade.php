@extends('layouts.app')

@section('content')
@php($filters = $filters ?? [])
@php($minFiltersError = $minFiltersError ?? null)

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0"><i class="fas fa-search text-primary me-2"></i>Buscar Orden</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver a la lista
        </a>
    </div>
    <div class="card-body">
        @if(!empty($minFiltersError))
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <span>{{ $minFiltersError }}</span>
            </div>
        @endif
        <form action="{{ route('orders.search') }}" method="GET" class="mb-0">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="invoice_number" class="form-label">Número de Factura</label>
                    <input type="text" name="invoice_number" id="invoice_number"
                           class="form-control" placeholder="Ejemplo: FAC-1234"
                           value="{{ $filters['invoice_number'] ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="customer_custom_id" class="form-label">ID personalizado del Cliente</label>
                    <input type="text" name="customer_custom_id" id="customer_custom_id"
                           class="form-control" placeholder="Ejemplo: CLI-001"
                           value="{{ $filters['customer_custom_id'] ?? '' }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="customer_number" class="form-label">Número de Cliente</label>
                    <input type="text" name="customer_number" id="customer_number"
                           class="form-control" placeholder="Ejemplo: CUST-001"
                           value="{{ $filters['customer_number'] ?? '' }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status_id" class="form-label">Estado</label>
                    @php($statuses = \App\Models\OrderStatus::all())
                    <select name="status_id" id="status_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ ($filters['status_id'] ?? '') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_from" class="form-label">Desde</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to" class="form-label">Hasta</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="{{ $filters['date_to'] ?? '' }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
