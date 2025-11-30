@extends('layouts.app')

@section('content')
@php($userRole = optional(Auth::user()->role)->name)
<div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0"><i class="fas fa-box-open text-primary me-2"></i>Detalles de la Orden #{{ $order->invoice_number }}</h1>
        <div class="d-flex align-items-center">
            @if($userRole === 'Admin' || ($userRole === 'Sales' && optional($order->status)->name === 'Ordered'))
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            @endif
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a la lista
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @php($badgeColor = $order->status->color ?? '#6c757d')
                            <span class="badge mb-2 fs-6" style="background-color: {{ $badgeColor }}; color: #fff;">
                                {{ $order->status->name ?? 'Sin estado' }}
                            </span>
                            @if($order->has_incident)
                                <span class="badge bg-warning text-dark mb-2">Incidencia registrada</span>
                            @endif
                            <p class="mb-1"><strong>Factura:</strong> {{ $order->invoice_number }}</p>
                            <p class="mb-1"><strong>Fecha de Orden:</strong> {{ $order->order_date ? date('d/m/Y', strtotime($order->order_date)) : date('d/m/Y', strtotime($order->created_at)) }}</p>
                            <p class="mb-1"><strong>Fecha de Creación:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-0"><strong>Última Actualización:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1"><strong>Nombre:</strong> {{ $order->customer_name }}</p>
                            <p class="mb-1"><strong>ID personalizado:</strong> {{ $order->customer_custom_id ?? 'No asignado' }}</p>
                            <p class="mb-1"><strong>Teléfono:</strong> {{ $order->customer_number ?? 'No especificado' }}</p>
                            <p class="mb-1"><strong>Dirección de Entrega:</strong> {{ $order->delivery_address ?? 'No especificada' }}</p>
                            <p class="mb-0"><strong>Datos Fiscales:</strong> {{ $order->fiscal_data ?? optional($order->client)->fiscal_data ?? 'No especificados' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Detalles Adicionales</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1"><strong>Empleado Asignado:</strong> {{ $order->user->name ?? 'No asignado' }}</p>
                            <p class="mb-0"><strong>Notas:</strong> {{ $order->notes ?? 'Sin notas adicionales' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>Logística</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Operador de ruta:</strong> {{ optional($order->routeOperator)->name ?? 'No asignado' }}</p>
                        <p class="mb-2"><strong>Incidencia:</strong> {{ $order->has_incident ? 'Sí' : 'No' }}</p>
                        @if($order->missing_items)
                            <div class="mb-2">
                                <strong>Faltantes:</strong>
                                <p class="mb-0 text-muted">{{ $order->missing_items }}</p>
                            </div>
                        @endif
                        @if($order->incident_notes)
                            <div>
                                <strong>Notas de incidencia:</strong>
                                <p class="mb-0 text-muted">{{ $order->incident_notes }}</p>
                            </div>
                        @endif
                        @if(in_array($userRole, ['Admin', 'Warehouse'], true))
                            <hr>
                            <h6 class="fw-bold">Asignar/actualizar ruta</h6>
                            <form method="POST" action="{{ route('orders.changeStatus', $order) }}">
                                @csrf
                                <input type="hidden" name="status_id" value="{{ $order->status_id }}">
                                <div class="mb-3">
                                    <label class="form-label">Operador de ruta</label>
                                    <select name="route_user_id" class="form-select">
                                        <option value="">Sin asignar</option>
                                        @foreach($routeOperators as $operator)
                                            <option value="{{ $operator->id }}" {{ $order->route_user_id === $operator->id ? 'selected' : '' }}>{{ $operator->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notas</label>
                                    <textarea name="status_notes" class="form-control" rows="2" placeholder="Ej. Ruta reasignada por disponibilidad"></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-route me-2"></i>Guardar asignación
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de cambios</h5>
                    </div>
                    <div class="card-body">
                        @if($order->histories->isEmpty())
                            <p class="text-muted mb-0">Sin movimientos registrados.</p>
                        @else
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Cambio</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->histories as $history)
                                        <tr>
                                            <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ optional($history->user)->name ?? 'Sistema' }}</td>
                                            <td>
                                                {{ optional($history->fromStatus)->name ?? 'N/A' }}
                                                <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                {{ optional($history->toStatus)->name ?? 'N/A' }}
                                            </td>
                                            <td>{{ $history->notes ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Productos y Costos</h5>
                    </div>
                    <div class="card-body">
                        @if($order->product)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>SKU</th>
                                            <th class="text-end">Precio unitario</th>
                                            <th class="text-end">Cantidad</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $order->product->name }}</td>
                                            <td>{{ $order->product->sku ?? '-' }}</td>
                                            <td class="text-end">${{ number_format((float)$order->product->price, 2) }}</td>
                                            <td class="text-end">{{ $order->quantity }}</td>
                                            <td class="text-end">${{ number_format((float)$order->product->price * (int)$order->quantity, 2) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total</th>
                                            <th class="text-end">${{ number_format((float)($total ?? 0), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Esta orden no tiene productos asociados.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($isRouteOwner)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-route me-2"></i>Acciones para la ruta</h5>
                        <span class="badge bg-primary">Tus pedidos asignados</span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Captura incidencias y cierra el pedido directamente desde este panel.</p>
                        @if($deliveredStatusId && optional($order->status)->name === 'In route')
                        <form method="POST" action="{{ route('orders.changeStatus', $order) }}" class="mb-4">
                            @csrf
                            <input type="hidden" name="status_id" value="{{ $deliveredStatusId }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Notas de estado</label>
                                    <textarea name="status_notes" class="form-control" rows="2" placeholder="Ej. Entregado en sitio, cliente ausente">{{ old('status_notes') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Artículos faltantes</label>
                                    <textarea name="missing_items" class="form-control" rows="2" placeholder="Detalle faltantes o daños">{{ old('missing_items') }}</textarea>
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-8">
                                    <label class="form-label">Notas de incidencia</label>
                                    <textarea name="incident_notes" class="form-control" rows="2" placeholder="Cliente no estaba, materiales incompletos, etc.">{{ old('incident_notes') }}</textarea>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="has_incident" id="route-has-incident" value="1" {{ old('has_incident', false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="route-has-incident">Marcar incidencia</label>
                                        <small class="text-muted d-block">El pedido permanecerá en In route hasta resolverla.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-flag-checkered me-2"></i>Registrar entrega con notas
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Imagen de Inicio</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        @if($order->start_image)
                            <img src="{{ asset('storage/' . $order->start_image) }}" alt="Imagen de inicio" class="img-fluid rounded" style="max-height: 300px">
                        @else
                            <div class="text-center text-muted">
                                <i class="fas fa-image fa-4x mb-3"></i>
                                <p>No hay imagen disponible</p>
                            </div>
                        @endif
                    </div>
                    @php($startPhotos = $order->photos->where('type', 'en_ruta'))
                    @if($startPhotos->isNotEmpty())
                        <div class="card-footer bg-white">
                            <h6 class="fw-bold">Notas registradas</h6>
                            <ul class="list-unstyled small mb-0">
                                @foreach($startPhotos as $photo)
                                    <li class="mb-2">
                                        <strong>{{ $photo->created_at->format('d/m/Y H:i') }}:</strong>
                                        {{ $photo->notes ?? 'Sin notas' }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Imagen de Entrega</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        @if($order->end_image)
                            <img src="{{ asset('storage/' . $order->end_image) }}" alt="Imagen de entrega" class="img-fluid rounded" style="max-height: 300px">
                        @else
                            <div class="text-center text-muted">
                                <i class="fas fa-image fa-4x mb-3"></i>
                                <p>No hay imagen disponible</p>
                            </div>
                        @endif
                    </div>
                    @php($endPhotos = $order->photos->where('type', 'entrega'))
                    @if($endPhotos->isNotEmpty())
                        <div class="card-footer bg-white">
                            <h6 class="fw-bold">Notas registradas</h6>
                            <ul class="list-unstyled small mb-0">
                                @foreach($endPhotos as $photo)
                                    <li class="mb-2">
                                        <strong>{{ $photo->created_at->format('d/m/Y H:i') }}:</strong>
                                        {{ $photo->notes ?? 'Sin notas' }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @auth
    @if(($userRole === 'Route' && $isRouteOwner) || $userRole === 'Admin')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Subir Evidencia</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.uploadPhoto', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="photo" class="form-label">Fotografía</label>
                                <input type="file" name="photo" id="photo" class="form-control" accept="image/*" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="photo_type" id="photo_type_start" value="start" checked>
                                        <label class="form-check-label" for="photo_type_start">Inicio</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="photo_type" id="photo_type_end" value="end">
                                        <label class="form-check-label" for="photo_type_end">Entrega</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas (opcional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Agregar detalles relevantes"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Subir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endauth
    </div>
</div>
@endsection
