@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <h1 class="h3 mb-0"><i class="fas fa-user-cog text-primary"></i> Mi perfil</h1>
        <p class="text-muted mb-0">Consulta tus datos, ajusta tu información y renueva tu contraseña sin salir del sistema.</p>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:90px;height:90px;">
                    <i class="fas fa-user fa-2x"></i>
                </div>
                <h5 class="mb-0">{{ $user->name }}</h5>
                <span class="badge bg-primary-subtle text-primary fw-semibold mt-1">
                    <i class="fas fa-id-badge"></i>
                    {{ optional($user->role)->name ?? 'Sin rol asignado' }}
                </span>
                @if($user->department)
                    <p class="text-muted small mt-2 mb-0"><i class="fas fa-sitemap"></i> {{ $user->department->name }}</p>
                @endif
                <hr>
                <div class="text-start small">
                    <p class="mb-2"><i class="fas fa-envelope text-muted me-2"></i> {{ $user->email }}</p>
                    <p class="mb-2"><i class="fas fa-calendar-check text-muted me-2"></i> Usuario activo desde {{ $user->created_at?->translatedFormat('d M Y') ?? 'N/A' }}</p>
                    <p class="mb-0"><i class="fas fa-user-shield text-muted me-2"></i> Estado: <strong>{{ $user->active ? 'Activo' : 'Inactivo' }}</strong></p>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h6 class="text-uppercase text-muted fw-semibold small"><i class="fas fa-lightbulb me-1"></i> Consejos de seguridad</h6>
                <ul class="small ps-3 mb-0">
                    <li>No compartas tus credenciales con terceros.</li>
                    <li>Verifica que tu correo esté actualizado para recibir alertas.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="fas fa-id-card-alt text-primary"></i> Información personal</h5>
                <small class="text-muted">Actualiza cómo te mostramos al resto del equipo.</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
