@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Resultado de Búsqueda</h4>
        </div>
        <div class="card-body">
            @if ($order)
                <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
                <p><strong>Estado:</strong> {{ $order->status->name }}</p>

                @if ($order->status->name === 'Delivered')
                    <p><strong>Foto de Entrega:</strong></p>
                    <img src="{{ asset('storage/' . $order->delivered_photo) }}" class="img-fluid">
                @elseif ($order->status->name === 'In process')
                    <p><strong>En proceso desde:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                @endif
            @else
                <p>No se encontró ningún pedido con ese número de factura.</p>
            @endif
        </div>
    </div>
</div>
@endsection
