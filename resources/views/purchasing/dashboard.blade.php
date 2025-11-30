@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="fas fa-shopping-cart text-primary me-2"></i>Panel de Compras</h1>
        <p class="text-muted mb-0">Supervisa niveles de inventario y órdenes pendientes antes de que impacten a logística.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Regresar
        </a>
        @if($showAll)
            <a href="{{ request()->fullUrlWithQuery(['show_all' => 0]) }}" class="btn btn-outline-primary">
                <i class="fas fa-filter me-1"></i>
                Mostrar solo críticos
            </a>
        @else
            <a href="{{ request()->fullUrlWithQuery(['show_all' => 1]) }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-1"></i>
                Ver todos los productos
            </a>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Productos por debajo del mínimo</p>
                <h2 class="fw-bold text-danger">{{ $summary['low_stock'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Total de productos monitoreados</p>
                <h2 class="fw-bold text-primary">{{ $summary['total_products'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Unidades comprometidas</p>
                <h2 class="fw-bold text-warning">{{ $summary['pending_units'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0"><i class="fas fa-boxes me-2"></i>Pedidos In process vigilados</h2>
                <span class="badge bg-info text-dark">Warehouse registra faltantes, Compras da seguimiento</span>
            </div>
            <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                @forelse($inProcessOrders as $order)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <strong>#{{ $order->invoice_number }}</strong>
                            <span class="text-muted">{{ $order->created_at->format('d/m') }}</span>
                        </div>
                        <p class="mb-1">{{ $order->client?->name ?? $order->customer_name }}</p>
                        <p class="small text-muted mb-1">{{ $order->product?->name ?? 'Producto sin asignar' }} · {{ $order->quantity }} uds</p>
                        @if($order->missing_items)
                            <p class="small text-danger mb-0"><i class="fas fa-exclamation-circle me-1"></i>{{ $order->missing_items }}</p>
                        @else
                            <p class="small text-muted mb-0">Sin faltantes documentados por almacén.</p>
                        @endif
                    </div>
                @empty
                    <p class="text-muted">Sin pedidos en proceso por ahora.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Inventario y alertas</h2>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark">Estados considerados: {{ implode(', ', $statusFilters) }}</span>
            @unless($showAll)
                <span class="badge bg-warning text-dark">Filtrando solo inventario crítico</span>
            @endunless
        </div>
    </div>
    @if(!$showAll && $summary['low_stock'] === 0)
        <div class="alert alert-info mx-3 mt-3">
            <i class="fas fa-check-circle me-1"></i>
            No hay productos por debajo del nivel mínimo. <a href="{{ request()->fullUrlWithQuery(['show_all' => 1]) }}" class="alert-link">Ver listado completo.</a>
        </div>
    @endif
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Nivel mínimo</th>
                    <th class="text-center">Unidades en órdenes</th>
                    <th>Órdenes relevantes</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php($pendingUnits = $product->orders->sum('quantity'))
                    <tr class="{{ $product->stock <= $product->reorder_level ? 'table-warning' : '' }}">
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <div class="text-muted small">Actualizado {{ $product->updated_at?->diffForHumans() }}</div>
                        </td>
                        <td>{{ $product->sku ?? '—' }}</td>
                        <td class="text-center fw-bold">{{ $product->stock }}</td>
                        <td class="text-center">{{ $product->reorder_level }}</td>
                        <td class="text-center">{{ $pendingUnits }}</td>
                        <td>
                            @forelse($product->orders->take(3) as $order)
                                <div class="small">
                                    <span class="badge bg-secondary">{{ $order->status?->name }}</span>
                                    {{ $order->client?->name ?? $order->customer_name }} · {{ $order->quantity }} uds
                                </div>
                            @empty
                                <span class="text-muted">Sin pedidos pendientes</span>
                            @endforelse
                            @if($product->orders->count() > 3)
                                <div class="small text-muted">+{{ $product->orders->count() - 3 }} más…</div>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('purchasing.alert', $product) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" {{ $product->stock > $product->reorder_level ? 'disabled' : '' }}>
                                    <i class="fas fa-bell me-1"></i> Reenviar alerta
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No se encontraron productos con los filtros actuales.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3 mt-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0"><i class="fas fa-tools me-2"></i>Materiales solicitados por Warehouse</h2>
                <span class="badge bg-primary">{{ $openRequests->count() }} abiertos</span>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                <p class="small text-muted">Estas solicitudes se crean automáticamente cuando Almacén documenta faltantes en un pedido.</p>
                @forelse($openRequests as $request)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $request->material_name }}</strong>
                                <span class="badge ms-2 bg-secondary text-uppercase">{{ strtoupper($request->status) }}</span>
                            </div>
                            <span class="text-muted">Pedido #{{ optional($request->order)->invoice_number ?? 'N/A' }}</span>
                        </div>
                        <p class="mb-1">Cliente: {{ $request->order?->client?->name ?? $request->order?->customer_name ?? '—' }}</p>
                        <p class="mb-1">Cantidad requerida: {{ $request->quantity_needed ?? '—' }}</p>
                        @if($request->needed_by)
                            <p class="mb-1 text-muted">Fecha objetivo: {{ $request->needed_by->format('d/m/Y') }}</p>
                        @endif
                        @if($request->details)
                            <p class="mb-2 small text-muted">Notas: {{ $request->details }}</p>
                        @endif
                        <p class="small text-muted mb-2">Asignado a: {{ $request->assignedTo?->name ?? 'Sin asignar' }}</p>

                        <div class="mb-2">
                            @foreach($request->updates->take(2) as $update)
                                <div class="small text-muted">
                                    <i class="fas fa-comment-dots me-1"></i>{{ $update->created_at->format('d/m H:i') }} · {{ $update->user?->name ?? 'Sistema' }}
                                    @if($update->status)
                                        <span class="badge bg-light text-dark ms-1">{{ $update->status }}</span>
                                    @endif
                                    @if($update->attachment_path)
                                        <a href="{{ asset('storage/' . $update->attachment_path) }}" target="_blank" class="ms-2"><i class="fas fa-paperclip"></i></a>
                                    @endif
                                    @if($update->notes)
                                        <div>{{ $update->notes }}</div>
                                    @endif
                                </div>
                            @endforeach
                            @if($request->updates->count() > 2)
                                <div class="small text-muted">+{{ $request->updates->count() - 2 }} actualizaciones adicionales</div>
                            @endif
                        </div>

                        <form action="{{ route('purchasing.requests.updates.store', $request) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Actualizar estado</label>
                                    <select name="status" class="form-select">
                                        <option value="">Sin cambio</option>
                                        <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="ordered" {{ $request->status === 'ordered' ? 'selected' : '' }}>Comprado</option>
                                        <option value="received" {{ $request->status === 'received' ? 'selected' : '' }}>Recibido</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Comprobante</label>
                                    <input type="file" name="attachment" class="form-control" accept="application/pdf,image/*">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="1" placeholder="Proveedor, factura, etc."></textarea>
                                </div>
                            </div>
                            <div class="text-end mt-2">
                                <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-save me-1"></i> Registrar avance</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">No hay solicitudes activas. Almacén no ha reportado faltantes recientes.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0"><i class="fas fa-history me-2"></i>Historial de compras</h2>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                @forelse($recentUpdates as $log)
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $log->purchaseRequest?->material_name ?? 'Solicitud' }}</strong>
                            <span class="badge bg-light text-dark text-uppercase">{{ $log->status ?? 'seguimiento' }}</span>
                        </div>
                        <p class="small text-muted mb-1">Pedido #{{ $log->purchaseRequest?->order?->invoice_number ?? 'N/A' }} · {{ $log->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-1">{{ $log->notes ?? 'Sin notas adicionales' }}</p>
                        <p class="small text-muted mb-0">Por: {{ $log->user?->name ?? 'Sistema' }}</p>
                        @if($log->attachment_path)
                            <a href="{{ asset('storage/' . $log->attachment_path) }}" target="_blank" class="small"><i class="fas fa-paperclip me-1"></i>Ver comprobante</a>
                        @endif
                    </div>
                @empty
                    <p class="text-muted">Aún no hay historial de compras registrado.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
