@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>Crear Nueva Orden</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver a la lista
        </a>
    </div>
    <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="d-flex">
                        <div class="me-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="invoice_number" class="form-label">Número de Factura</label>
                            <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="{{ old('invoice_number') }}">
                            <small class="text-muted">Si se deja vacío, se generará automáticamente</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="order_date" class="form-label">Fecha de Orden</label>
                            <input type="date" name="order_date" id="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="fiscal_data" class="form-label">Datos Fiscales del Cliente *</label>
                            <textarea name="fiscal_data" id="fiscal_data" class="form-control" rows="2" placeholder="RFC, razón social y domicilio fiscal">{{ old('fiscal_data') }}</textarea>
                            <small class="text-muted">Si lo dejas vacío copiaremos lo registrado en el cliente seleccionado.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Cliente *</label>
                            <select name="client_id" id="client_id" class="form-select" required>
                                <option value="">-- Seleccione un cliente --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->phone }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <a href="{{ route('clients.create') }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-plus"></i> Crear nuevo cliente
                                </a>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Producto *</label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                <option value="">-- Seleccione un producto --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} - ${{ number_format($product->price, 2) }} (Stock: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <a href="{{ route('products.create') }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-plus"></i> Crear nuevo producto
                                </a>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad *</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required value="{{ old('quantity', 1) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Dirección de Entrega</label>
                            <input type="text" name="delivery_address" id="delivery_address" class="form-control" value="{{ old('delivery_address') }}">
                            <small class="text-muted">Opcional - Se usará la dirección del cliente si se deja vacío</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status_id" class="form-label">Estado *</label>
                            <select name="status_id" id="status_id" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Empleado Asignado *</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @if(optional(Auth::user()->role)->name === 'Admin')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="route_user_id" class="form-label">Operador de Ruta (opcional)</label>
                            <select name="route_user_id" id="route_user_id" class="form-select">
                                <option value="">-- Seleccione --</option>
                                @foreach($users as $routeUser)
                                    @if(optional($routeUser->role)->name === 'Route')
                                        <option value="{{ $routeUser->id }}" {{ old('route_user_id') == $routeUser->id ? 'selected' : '' }}>
                                            {{ $routeUser->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="has_incident" id="has_incident" value="1" {{ old('has_incident') ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_incident">Marcar incidencia desde la creación</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="missing_items" class="form-label">Faltantes reportados</label>
                            <textarea name="missing_items" id="missing_items" class="form-control" rows="2" placeholder="Detalle de materiales faltantes">{{ old('missing_items') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="incident_notes" class="form-label">Notas de incidencia</label>
                            <textarea name="incident_notes" id="incident_notes" class="form-control" rows="2" placeholder="Describe la incidencia">{{ old('incident_notes') }}</textarea>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label for="notes" class="form-label">Notas Adicionales</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_image" class="form-label">Imagen de inicio (opcional)</label>
                            <input type="file" name="start_image" id="start_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_image" class="form-label">Imagen de entrega (opcional)</label>
                            <input type="file" name="end_image" id="end_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Orden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
