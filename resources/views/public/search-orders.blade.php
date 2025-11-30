@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0 text-center">Búsqueda de Pedidos</h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Halcon Logo" class="img-fluid" style="max-height: 80px;">
                    </div>

                    <form action="{{ route('orders.search') }}" method="GET">
                        <div class="mb-3">
                            <label for="query" class="form-label">Buscar por Número de Factura o Nombre de Cliente</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="query" name="query" placeholder="Ingrese número de factura o nombre de cliente" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            <div class="form-text">Ingrese el número de factura completo o parte del nombre del cliente</div>
                        </div>
                    </form>

                    @if(session('error'))
                    <div class="alert alert-danger mt-3">
                        {{ session('error') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection