@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Detalle de Estado</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $status->name }}</p>
            <p><strong>Color:</strong> <span class="badge" style="background-color: {{ $status->color ?? '#6c757d' }};">{{ $status->color ?? '—' }}</span></p>
        </div>
    </div>
    <a href="{{ route('statuses.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection