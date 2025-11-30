@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="mb-4">Consulta de Pedido</h1>
    <form action="{{ route('orders.search') }}" method="GET" class="d-flex justify-content-center">
        <input type="text" name="invoice_number" class="form-control w-50 me-2" placeholder="Número de factura">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
</div>
@endsection
