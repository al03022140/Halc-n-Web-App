@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0 text-center">Consulta el Estado de tu Pedido</h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Halcon Logo" class="img-fluid" style="max-height: 100px;">
                        <p class="lead mt-3">Ingresa tu ID personalizado de cliente y número de factura para consultar el estado de tu pedido</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('track.order') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="customer_custom_id" class="form-label">ID personalizado del Cliente</label>
                            <input type="text" class="form-control form-control-lg" id="customer_custom_id" name="customer_custom_id" value="{{ old('customer_custom_id') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="invoice_number" class="form-label">Número de Factura</label>
                            <input type="text" class="form-control form-control-lg" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Consultar Pedido</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection