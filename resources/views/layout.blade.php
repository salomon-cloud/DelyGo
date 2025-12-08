<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DelyGo - Delivery</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('extra-css')
</head>
<body>
    <header>
        <h1><a href="/" style="text-decoration: none; color: inherit;">🍕 DelyGo - Delivery</a></h1>
        <nav>
            @if (Auth::check())
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <!-- Dashboard Principal -->
                    <a href="{{ route('dashboard') }}" class="nav-link" style="font-weight: 600;">Dashboard</a>

                    @if (Auth::user()->rol === 'cliente')
                        <a href="{{ route('cliente.dashboard') }}" class="nav-link">Mi Panel</a>
                        <a href="{{ route('cliente.orden.create') }}" class="nav-link">+ Nueva Orden</a>
                        <a href="{{ route('cliente.ordenes.index') }}" class="nav-link">Mis Órdenes</a>
                    @elseif (Auth::user()->rol === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-link" style="font-weight: 600;">Admin</a>
                        <a href="{{ route('admin.restaurantes') }}" class="nav-link">Restaurantes</a>
                        <a href="{{ route('admin.productos') }}" class="nav-link">Productos</a>
                        <a href="{{ route('admin.users') }}" class="nav-link">Usuarios</a>
                        <a href="{{ route('admin.asignacion') }}" class="nav-link">Asignaciones</a>
                    @elseif (Auth::user()->rol === 'repartidor')
                        <a href="{{ route('repartidor.dashboard') }}" class="nav-link" style="font-weight: 600;">Mi Panel</a>
                        <a href="{{ route('repartidor.ordenes') }}" class="nav-link">Mis Órdenes</a>
                        <a href="{{ route('repartidor.historial') }}" class="nav-link">Historial</a>
                    @elseif (Auth::user()->rol === 'restaurante')
                        <a href="{{ route('restaurante.dashboard') }}" class="nav-link" style="font-weight: 600;">Mi Panel</a>
                        <a href="{{ route('productos.index') }}" class="nav-link">Mis Productos</a>
                        <a href="{{ route('restaurante.ordenes.pendientes') }}" class="nav-link">Órdenes Pendientes</a>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="nav-link">Perfil</a>

                    <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="width: auto; padding: 0.5rem 1rem;">Salir</button>
                    </form>
                </div>
            @else
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('login') }}" class="nav-link">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
                </div>
            @endif
        </nav>
    </header>

    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                <strong>Error:</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                ✗ {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <footer>
        <p>&copy; 2025 DelyGo - Sistema de Delivery Simplificado | v1.0</p>
    </footer>

    <style>
    .nav-link {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: background 0.3s;
    }
    
    .nav-link:hover {
        background: rgba(255,255,255,0.1);
    }
    
    header nav {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    </style>

    @yield('extra-js')
</body>
</html>
