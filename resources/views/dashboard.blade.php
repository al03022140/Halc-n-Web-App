@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary rounded-circle p-3 text-white">
                            <i class="fas fa-tachometer-alt fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h1 class="h3 mb-0">Bienvenido, {{ Auth::check() ? Auth::user()->name : 'Invitado' }}</h1>
                        <p class="text-muted mb-0">Panel de Control</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @if(Auth::check())
        @php($roleName = optional(Auth::user()->role)->name ?? '')

        @if($roleName === 'Route')
        <div class="col-lg-10 mx-auto mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center gap-4">
                    <div class="bg-success text-white rounded-circle p-4">
                        <i class="fas fa-route fa-3x"></i>
                    </div>
                    <div>
                        <h2 class="h3 mb-2">Bienvenido al panel de ruta</h2>
                        <p class="text-muted mb-3">Desde aquí puedes ver los pedidos asignados, documentar incidencias y subir evidencia de entrega.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('orders.index') }}" class="btn btn-success"><i class="fas fa-list me-2"></i>Ver pedidos asignados</a>
                            <a href="{{ route('orders.search') }}" class="btn btn-outline-secondary"><i class="fas fa-search me-2"></i>Buscar pedido</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-clipboard-list text-success me-2"></i>Pasos rápidos</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Revisa tus pedidos en <strong>Órdenes</strong>.</p>
                    <p class="mb-2"><i class="fas fa-camera text-primary me-2"></i>Sube evidencia desde el detalle del pedido.</p>
                    <p class="mb-2"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Registra incidencias antes de marcar Delivered.</p>
                    <p class="mb-0"><i class="fas fa-info-circle text-muted me-2"></i>Solo puedes cerrar pedidos que estén en estado <strong>In route</strong>.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-lightbulb text-primary me-2"></i>Consejos útiles</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Carga evidencia clara</h6>
                        <p class="text-muted mb-0">Incluye fotos de inicio y entrega. Usa las notas para describir incidencias o firmas del cliente.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Documenta incidencias</h6>
                        <p class="text-muted mb-0">Marca "Marcar incidencia" y explica qué ocurrió antes de registrar la entrega.</p>
                    </div>
                    <div class="mb-0">
                        <h6 class="fw-bold">Comunicación con Almacén</h6>
                        <p class="text-muted mb-0">Si un pedido no aparece en tu panel, contacta a Almacén para que lo asignen a tu ruta.</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Estadísticas rápidas --}}
        <div class="col-12 mb-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Órdenes Totales</h6>
                                    <h2 class="mb-0 mt-2">{{ App\Models\Order::count() }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-box-open fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Órdenes Completadas</h6>
                                    @php($deliveredId = \App\Models\OrderStatus::where('name','Delivered')->value('id'))
                                    <h2 class="mb-0 mt-2">{{ \App\Models\Order::where('status_id', $deliveredId)->count() }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-warning text-dark">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Órdenes Pendientes</h6>
                                    @php($orderedId = \App\Models\OrderStatus::where('name','Ordered')->value('id'))
                                    <h2 class="mb-0 mt-2">{{ \App\Models\Order::where('status_id', $orderedId)->count() }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-clock fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-info text-white">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Usuarios Activos</h6>
                                    <h2 class="mb-0 mt-2">{{ App\Models\User::count() }}</h2>
                                </div>
                                <div>
                                    <i class="fas fa-users fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        @if($roleName === 'Admin')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-users text-primary me-2"></i>Usuarios</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Gestiona los usuarios activos e inactivos, asigna roles y departamentos.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary"><i class="fas fa-list me-2"></i>Ver Usuarios</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Órdenes --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-box text-success me-2"></i>Órdenes</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Consulta, crea y actualiza las órdenes de clientes.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-success"><i class="fas fa-list me-2"></i>Ver Órdenes</a>
                        @if(in_array($roleName, ['Admin', 'Sales'], true))
                        <a href="{{ route('orders.create') }}" class="btn btn-success"><i class="fas fa-plus me-2"></i>Nueva Orden</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Pedidos Archivados --}}
        @if($roleName === 'Admin')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-archive text-warning me-2"></i>Pedidos Archivados</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Consulta y recupera pedidos eliminados lógicamente.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.archived') }}" class="btn btn-outline-warning"><i class="fas fa-folder-open me-2"></i>Ver Archivados</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reportes y Configuración --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Reportes & Configuración</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Genera reportes rápidos y ajusta reglas de negocio.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.orders') }}" class="btn btn-outline-primary"><i class="fas fa-chart-bar me-2"></i>Reporte de pedidos</a>
                        <a href="{{ route('settings.system') }}" class="btn btn-primary"><i class="fas fa-cog me-2"></i>Parámetros del sistema</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Productos y Clientes --}}
        @if($roleName === 'Admin')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-tags text-info me-2"></i>Productos y Clientes</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Registra nuevos productos y clientes en el sistema.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-outline-info"><i class="fas fa-box-open me-2"></i>Agregar Productos</a>
                        <a href="{{ route('clients.create') }}" class="btn btn-info"><i class="fas fa-user-plus me-2"></i>Agregar Clientes</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
    @else
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0"><i class="fas fa-search text-primary me-2"></i>Buscar Pedido</h5>
                </div>
                <div class="card-body p-4">
                    <p class="card-text">Ingresa el número de factura para consultar el estado de tu pedido.</p>
                    <form action="{{ route('search') }}" method="POST" class="mt-3">
                        @csrf
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-primary text-white"><i class="fas fa-file-invoice"></i></span>
                            <input type="text" name="invoice_number" class="form-control form-control-lg" placeholder="Número de factura" required>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search me-2"></i>Buscar</button>
                        </div>
                    </form>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Si eres un empleado, inicia sesión para acceder a todas las funcionalidades del sistema.
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
