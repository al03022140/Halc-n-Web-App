@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Subir Evidencia Fotográfica - Pedido #{{ $order->id }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Información del Pedido</h5>
                        <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
                        <p><strong>Factura:</strong> {{ $order->invoice_number }}</p>
                        <p><strong>Estado:</strong> {{ $order->status->name }}</p>
                    </div>

                    <form action="{{ route('orders.uploadPhoto', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="photo_type" class="form-label">Tipo de Evidencia</label>
                            <select name="photo_type" id="photo_type" class="form-select" required>
                                <option value="">Seleccionar tipo de evidencia</option>
                                <option value="start">Carga de Material (Inicio de Ruta)</option>
                                <option value="end">Entrega de Material (Evidencia de Entrega)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="photo" class="form-label">Fotografía</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/*" required>
                            <div class="form-text">Toma una foto clara del material cargado o entregado.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas (opcional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Subir Evidencia</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection