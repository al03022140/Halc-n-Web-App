@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center g-4">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Halcón Mensajería</h3>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="text-center mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Halcon Logo" class="img-fluid" style="max-height: 110px;">
                        <p class="lead mt-3 mb-0">Consulta el estatus de tu pedido en tiempo real.</p>
                    </div>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item px-0"><i class="fas fa-check text-success me-2"></i>Validamos que la orden pertenezca a tu ID personalizado de cliente.</li>
                        <li class="list-group-item px-0"><i class="fas fa-bolt text-warning me-2"></i>Actualización instantánea sin recargar la página.</li>
                        <li class="list-group-item px-0"><i class="fas fa-lock text-primary me-2"></i>Solo se muestra información autorizada.</li>
                    </ul>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <div class="mt-auto text-center">
                        @guest
                            <p class="text-muted mb-2">¿Eres parte del equipo Halcón?</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar sesión en el panel
                            </a>
                        @endguest
                        @auth
                            <p class="text-muted mb-2">Ya iniciaste sesión.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-home me-2"></i>Ir al panel administrativo
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div id="order-tracker-app" data-endpoint="{{ route('api.orders.track') }}">
                <noscript>
                    <div class="alert alert-warning">
                        Activa JavaScript para usar el rastreador interactivo.
                    </div>
                </noscript>
            </div>
        </div>
    </div>
</div>

@push('head')
    @vite('resources/js/public-tracker/main.js')
@endpush
@endsection