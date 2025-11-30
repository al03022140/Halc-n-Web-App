@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Detalle de Departamento</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $department->name }}</p>
        </div>
    </div>
    <a href="{{ route('departments.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection