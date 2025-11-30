{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Pedidos | Halcón</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Halcón</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Consulta de Pedido</h1>

        {{-- Formulario de búsqueda --}}
        <form action="{{ route('orders.search') }}" method="GET" class="row justify-content-center mb-5">
            <div class="col-md-6">
                <input type="text" name="invoice_number" class="form-control form-control-lg" placeholder="Número de factura" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-lg w-100">Buscar</button>
            </div>
        </form>

        {{-- Resultado de la búsqueda --}}
        @isset($order)
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Factura: {{ $order->invoice_number }}</h5>
                    <p class="card-text">Estado: <strong>{{ $order->status->name }}</strong></p>

                    @if($order->status->name === 'Entregado' && $order->photo)
                        <p><strong>Foto de Entrega:</strong></p>
                        <img src="{{ asset('storage/' . $order->photo->path) }}" alt="Foto de entrega" class="img-fluid rounded">
                    @elseif($order->status->name === 'En Proceso')
                        <p><strong>Proceso:</strong> {{ $order->status->name }}</p>
                        <p><strong>Fecha:</strong> {{ $order->updated_at->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        @endisset
    </div>

</body>
</html>
