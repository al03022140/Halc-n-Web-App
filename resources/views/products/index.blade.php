@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0"><i class="fas fa-box text-primary me-2"></i>Lista de Productos</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Nombre</th>
                        <th>SKU</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-3">{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku ?? 'N/A' }}</td>
                            <td>${{ number_format($product->price ?? 0, 2) }}</td>
                            <td>{{ $product->stock ?? 0 }}</td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                        onclick="if(confirm('¿Está seguro de eliminar este producto?')) { document.getElementById('delete-product-{{ $product->id }}').submit(); }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-product-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5>No hay productos disponibles</h5>
                                    <p class="text-muted mb-2">Crea un nuevo producto para comenzar</p>
                                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i>Nuevo Producto
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection