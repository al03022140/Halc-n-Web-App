<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Halcon WebApp</title>

    <!-- Bootstrap CSS por CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .btn {
            border-radius: 5px;
            font-weight: 500;
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
    </style>

    @stack('head')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">🦅 Halcón</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    @php($roleName = optional(Auth::user()->role)->name ?? '')
                @endauth
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Inicio</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('orders.index') }}"><i class="fas fa-box"></i> Órdenes</a>
                    </li>
                    @if($roleName === 'Admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}"><i class="fas fa-users"></i> Usuarios</a>
                    </li>
                    @endif
                    @if($roleName === 'Admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clients.index') }}"><i class="fas fa-address-book"></i> Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}"><i class="fas fa-tags"></i> Productos</a>
                    </li>
                    @endif
                    @if(in_array($roleName, ['Admin', 'Purchasing']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('purchasing.dashboard') }}"><i class="fas fa-shopping-cart"></i> Compras</a>
                    </li>
                    @endif
                    @if(in_array($roleName, ['Admin', 'Warehouse']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warehouse.picks') }}"><i class="fas fa-warehouse"></i> Almacén</a>
                    </li>
                    @endif
                    @if($roleName === 'Admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.orders') }}"><i class="fas fa-chart-bar"></i> Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.system') }}"><i class="fas fa-cog"></i> Configuración</a>
                    </li>
                    @endif
                    <li class="nav-item ms-lg-auto">
                        <a class="nav-link" href="{{ route('orders.search') }}"><i class="fas fa-search"></i> Buscar</a>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Iniciar sesión</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-cog"></i> Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-light mt-5 py-3 border-top">
        <div class="container text-center text-muted">
            <p class="mb-0">© {{ date('Y') }} Halcón WebApp - Todos los derechos reservados</p>
        </div>
    </footer>

    <!-- Bootstrap JS por CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
      (function(){
        const MAX_BYTES = 4 * 1024 * 1024; // 4 MB
        const inputs = ['start_image','end_image','photo']
          .map(id => document.getElementById(id))
          .filter(Boolean);
        function validateSize(input){
          const file = input.files && input.files[0];
          if (!file) return;
          if (file.size > MAX_BYTES){
            input.value = '';
            const mb = (MAX_BYTES/1024/1024).toFixed(0);
            alert(`La imagen supera ${mb} MB. Selecciona una más ligera.`);
          }
        }
        inputs.forEach(i => i.addEventListener('change', () => validateSize(i)));
      })();
    </script>
        @stack('scripts')
  </body>
</html>
