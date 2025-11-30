@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="fas fa-warehouse text-primary me-2"></i>Panel de Almacén</h1>
        <p class="text-muted mb-0">Visualiza pedidos listos para surtir, registra faltantes y prepara la salida del pedido.</p>
        <p class="small text-muted mb-0">Nota: el estado <strong>Delivered</strong> se muestra solo para consulta; Warehouse no puede marcar entregas.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Regresar</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="btn-group" role="group" aria-label="Filtros por estado">
                @foreach($statusNames as $name)
                    @php($statusColor = $statusMap[$name]->color ?? null)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $name]) }}"
                       class="btn btn-sm {{ $statusFilter === $name ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $name }}
                        <span class="badge ms-1 text-dark" style="background-color: {{ $statusColor ?? '#f8f9fa' }}; color: {{ $statusColor ? '#fff' : '#000' }}">{{ $statusSummary[$name] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('warehouse.picks') }}" class="d-flex gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="search" name="query" class="form-control" placeholder="Buscar por cliente, factura o ruta" value="{{ request('query') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first() }}
    </div>
@endif

@if($orders->isEmpty())
    <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay pedidos en {{ $statusFilter }} para mostrar.</div>
@else
    <div class="row g-3">
        @foreach($orders as $order)
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h2 class="h5 mb-0">{{ $order->customer_name }}</h2>
                                @php($statusName = $order->display_status_name)
                                @php($scolor = $order->display_status_color ?? '#6c757d')
                                <span class="badge" style="background-color: {{ $scolor }}; color: #fff;">
                                    {{ $statusName }}
                                </span>
                            </div>
                            <span class="text-muted">#{{ $order->invoice_number }}</span>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1"><strong>Producto:</strong> {{ $order->product?->name ?? 'Sin asignar' }}</p>
                            <p class="mb-1"><strong>Cantidad:</strong> {{ $order->quantity }} uds</p>
                            <p class="mb-1"><strong>Entrega:</strong> {{ $order->delivery_address ?? 'Pendiente' }}</p>
                            @if($order->purchaseRequest)
                                <div class="mt-2 p-2 border rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">Compras · {{ $order->purchaseRequest->material_name }}</span>
                                        <span class="badge bg-secondary text-uppercase">{{ $order->purchaseRequest->status }}</span>
                                    </div>
                                    <p class="small mb-1">Cantidad solicitada: {{ $order->purchaseRequest->quantity_needed ?? '—' }}</p>
                                    @if($order->purchaseRequest->details)
                                        <p class="small mb-1 text-muted">Notas: {{ $order->purchaseRequest->details }}</p>
                                    @endif
                                    @php($latestUpdate = $order->purchaseRequest->updates->first())
                                    @if($latestUpdate)
                                        <div class="small text-muted">
                                            <i class="fas fa-comment-dots me-1"></i>{{ $latestUpdate->created_at->format('d/m H:i') }} · {{ $latestUpdate->user?->name ?? 'Compras' }}
                                            @if($latestUpdate->status)
                                                <span class="badge bg-light text-dark ms-1">{{ $latestUpdate->status }}</span>
                                            @endif
                                            @if($latestUpdate->attachment_path)
                                                <a href="{{ asset('storage/' . $latestUpdate->attachment_path) }}" target="_blank" class="ms-2"><i class="fas fa-paperclip"></i></a>
                                            @endif
                                            @if($latestUpdate->notes)
                                                <div>{{ $latestUpdate->notes }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @php($isCurrent = (int) old('order_id') === $order->id)
                        <form method="POST" action="{{ route('warehouse.orders.logistics', $order) }}">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <div class="mb-3">
                                <label class="form-label">Artículos faltantes / ajustes</label>
                                <textarea name="missing_items" class="form-control" rows="2" placeholder="Ej. 2 cajas dañadas">{{ $isCurrent ? old('missing_items') : $order->missing_items }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notas para Compras</label>
                                <textarea name="incident_notes" class="form-control" rows="2" placeholder="Describe faltantes, urgencias o proveedores sugeridos">{{ $isCurrent ? old('incident_notes') : $order->incident_notes }}</textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-clipboard-check me-1"></i> Guardar cambios
                                </button>
                                @php($currentRole = optional(Auth::user()->role)->name)
                                @if($currentRole === 'Warehouse')
                                    @php($currentStatus = optional($order->status)->name)
                                    @if($currentStatus === 'Ordered' && isset($statusMap['In process']))
                                        <form method="POST" action="{{ route('orders.changeStatus', $order) }}" style="display:inline-block">
                                            @csrf
                                            <input type="hidden" name="status_id" value="{{ $statusMap['In process']->id }}">
                                            <input type="hidden" name="status_notes" value="Avanzado a In process desde panel de Almacén">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-play me-1"></i> Marcar In process
                                            </button>
                                        </form>
                                    @elseif($currentStatus === 'In process' && isset($statusMap['In route']))
                                        <form method="POST" action="{{ route('orders.changeStatus', $order) }}" style="display:inline-block">
                                            @csrf
                                            <input type="hidden" name="status_id" value="{{ $statusMap['In route']->id }}">
                                            <input type="hidden" name="status_notes" value="Avanzado a In route desde panel de Almacén">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-truck me-1"></i> Marcar In route
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-link">Ver detalle completo</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
