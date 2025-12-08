@extends('layout')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h2 class="card-title">🔧 Panel Administrativo</h2>
        <p style="color: #666; margin-top: 0.5rem;">Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <!-- Cards de Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="text-align: center; border-left: 4px solid var(--primary-color);">
            <h3 style="font-size: 2rem; color: var(--primary-color);">{{ $totalRestaurantes }}</h3>
            <p style="margin: 0; color: #666;">Restaurantes</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--secondary-color);">
            <h3 style="font-size: 2rem; color: var(--secondary-color);">{{ $totalProductos }}</h3>
            <p style="margin: 0; color: #666;">Productos</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--info-color);">
            <h3 style="font-size: 2rem; color: var(--info-color);">{{ $totalOrdenes }}</h3>
            <p style="margin: 0; color: #666;">Órdenes Total</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--success-color);">
            <h3 style="font-size: 2rem; color: var(--success-color);">{{ $totalRepartidores }}</h3>
            <p style="margin: 0; color: #666;">Repartidores</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--warning-color);">
            <h3 style="font-size: 2rem; color: var(--warning-color);">{{ $totalUsuarios }}</h3>
            <p style="margin: 0; color: #666;">Usuarios</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid #8B5CF6;">
            <h3 style="font-size: 2rem; color: #8B5CF6;">${{ number_format($ventasTotal, 2) }}</h3>
            <p style="margin: 0; color: #666;">Ventas Total</p>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">⚡ Acciones Rápidas</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('admin.users') }}" class="btn btn-primary">👥 Gestionar Usuarios</a>
            <a href="{{ route('admin.restaurantes') }}" class="btn btn-secondary">🍽️ Restaurantes</a>
            <a href="{{ route('admin.productos') }}" class="btn btn-secondary">📦 Productos</a>
            <a href="{{ route('admin.asignacion') }}" class="btn btn-info">🚗 Asignaciones</a>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline">Perfil</a>
        </div>
    </div>

    <!-- Grid de Secciones de Gestión -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Gestión de Usuarios -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); border-top: 3px solid var(--primary-color);">
            <h3 style="margin: 0 0 1rem 0; color: var(--primary-color);">👥 Usuarios</h3>
            <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <p style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 600; color: var(--primary-color);">{{ $totalUsuarios }}</p>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Usuarios registrados</p>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                <li style="margin-bottom: 0.5rem;">• Clientes: {{ $clientesCount }}</li>
                <li style="margin-bottom: 0.5rem;">• Restaurantes: {{ $restaurantesCount }}</li>
                <li style="margin-bottom: 0.5rem;">• Repartidores: {{ $repartidoresCount }}</li>
                <li>• Admins: {{ $adminsCount }}</li>
            </ul>
            <a href="{{ route('admin.users') }}" class="btn btn-small btn-primary" style="width: 100%; text-align: center; margin-top: 1rem;">Gestionar</a>
        </div>

        <!-- Gestión de Restaurantes -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); border-top: 3px solid var(--secondary-color);">
            <h3 style="margin: 0 0 1rem 0; color: var(--secondary-color);">🍽️ Restaurantes</h3>
            <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <p style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 600; color: var(--secondary-color);">{{ $totalRestaurantes }}</p>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Restaurantes activos</p>
            </div>
            <div style="font-size: 0.85rem; color: #666; margin-bottom: 1rem;">
                <p style="margin: 0.5rem 0;">Productos: {{ $totalProductos }}</p>
                <p style="margin: 0.5rem 0;">Órdenes: {{ $ordenesRestaurantes }}</p>
            </div>
            <a href="{{ route('admin.restaurantes') }}" class="btn btn-small btn-secondary" style="width: 100%; text-align: center;">Gestionar</a>
        </div>

        <!-- Gestión de Órdenes -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); border-top: 3px solid var(--info-color);">
            <h3 style="margin: 0 0 1rem 0; color: var(--info-color);">📦 Órdenes</h3>
            <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <p style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 600; color: var(--info-color);">{{ $totalOrdenes }}</p>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Total de órdenes</p>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                <li style="margin-bottom: 0.5rem;">• Recibidas: {{ $ordenesRecibidas }}</li>
                <li style="margin-bottom: 0.5rem;">• En Prep: {{ $ordenesPreparando }}</li>
                <li style="margin-bottom: 0.5rem;">• Entregadas: {{ $ordenesEntregadas }}</li>
            </ul>
            <a href="{{ route('admin.asignacion') }}" class="btn btn-small btn-info" style="width: 100%; text-align: center; margin-top: 1rem;">Ver Órdenes</a>
        </div>

        <!-- Gestión de Repartidores -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); border-top: 3px solid var(--success-color);">
            <h3 style="margin: 0 0 1rem 0; color: var(--success-color);">🚗 Repartidores</h3>
            <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <p style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 600; color: var(--success-color);">{{ $totalRepartidores }}</p>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Repartidores disponibles</p>
            </div>
            <div style="font-size: 0.85rem; color: #666; margin-bottom: 1rem;">
                <p style="margin: 0.5rem 0;">Activos hoy: {{ $repartidoresActivos }}</p>
                <p style="margin: 0.5rem 0;">Entregas: {{ $totalEntregas }}</p>
            </div>
            <a href="{{ route('admin.users') }}" class="btn btn-small btn-success" style="width: 100%; text-align: center;">Ver Repartidores</a>
        </div>

        <!-- Asignación de Órdenes -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); border-top: 3px solid var(--warning-color);">
            <h3 style="margin: 0 0 1rem 0; color: var(--warning-color);">🔗 Asignaciones</h3>
            <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <p style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 600; color: var(--warning-color);">{{ $ordenesSinAsignar }}</p>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Órdenes sin repartidor</p>
            </div>
            <p style="margin: 0 0 1rem 0; color: #666; font-size: 0.9rem;">Asigna repartidores a órdenes pendientes</p>
            <a href="{{ route('admin.asignacion') }}" class="btn btn-small btn-warning" style="width: 100%; text-align: center;">Asignar</a>
        </div>

        <!-- Reportes -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); border-top: 3px solid #8B5CF6;">
            <h3 style="margin: 0 0 1rem 0; color: #8B5CF6;">📊 Reportes</h3>
            <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <p style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 600; color: #8B5CF6;">${{ number_format($ventasTotal, 2) }}</p>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Ventas totales</p>
            </div>
            <p style="margin: 0 0 1rem 0; color: #666; font-size: 0.9rem;">Promedio por orden: ${{ number_format($totalOrdenes > 0 ? $ventasTotal / $totalOrdenes : 0, 2) }}</p>
            <a href="#" class="btn btn-small btn-outline" style="width: 100%; text-align: center;">Exportar</a>
        </div>
    </div>

    <!-- Órdenes Recientes -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">📋 Órdenes Recientes</h3>
        
        @if($ordenesRecientes->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">ID</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Cliente</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Restaurante</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Estado</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Repartidor</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Total</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenesRecientes as $orden)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.75rem;"><strong>#{{ $orden->id }}</strong></td>
                            <td style="padding: 0.75rem;">{{ $orden->cliente->name }}</td>
                            <td style="padding: 0.75rem;">{{ $orden->restaurante->nombre }}</td>
                            <td style="padding: 0.75rem;">
                                <span class="estado-badge estado-{{ $orden->estado }}" style="padding: 0.25rem 0.75rem; font-size: 0.85rem;">
                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">{{ $orden->repartidor->name ?? '—' }}</td>
                            <td style="padding: 0.75rem; font-weight: 600;">${{ number_format($orden->total, 2) }}</td>
                            <td style="padding: 0.75rem; font-size: 0.9rem;">{{ $orden->created_at->format('d/m H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 2rem;">No hay órdenes aún</p>
        @endif
    </div>

    <!-- Actividad Reciente -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm);">
            <h3 style="margin-top: 0;">🆕 Últimos Usuarios</h3>
            @if($ultimosUsuarios->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($ultimosUsuarios as $usuario)
                    <div style="border-bottom: 1px solid #eee; padding-bottom: 0.75rem;">
                        <p style="margin: 0 0 0.25rem 0; font-weight: 600;">{{ $usuario->name }}</p>
                        <p style="margin: 0 0 0.25rem 0; font-size: 0.85rem; color: #666;">{{ $usuario->email }}</p>
                        <p style="margin: 0; font-size: 0.8rem; color: #999;">Rol: <span style="background: #f0f0f0; padding: 0.2rem 0.5rem; border-radius: 3px;">{{ $usuario->rol }}</span></p>
                    </div>
                    @endforeach
                </div>
            @else
                <p style="color: #999; text-align: center; padding: 2rem 0;">Sin usuarios nuevos</p>
            @endif
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm);">
            <h3 style="margin-top: 0;">⭐ Top Restaurantes</h3>
            @if(count($topRestaurantes) > 0)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($topRestaurantes as $index => $restaurante)
                    <div style="border-bottom: 1px solid #eee; padding-bottom: 0.75rem;">
                        <p style="margin: 0 0 0.25rem 0; font-weight: 600;">{{ $index + 1 }}. {{ $restaurante['nombre'] }}</p>
                        <p style="margin: 0; font-size: 0.9rem; color: #666;">{{ $restaurante['ordenes_count'] }} órdenes</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p style="color: #999; text-align: center; padding: 2rem 0;">Sin datos</p>
            @endif
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm);">
            <h3 style="margin-top: 0;">🎯 Top Repartidores</h3>
            @if(count($topRepartidores) > 0)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($topRepartidores as $index => $repartidor)
                    <div style="border-bottom: 1px solid #eee; padding-bottom: 0.75rem;">
                        <p style="margin: 0 0 0.25rem 0; font-weight: 600;">{{ $index + 1 }}. {{ $repartidor['name'] }}</p>
                        <p style="margin: 0; font-size: 0.9rem; color: #666;">{{ $repartidor['entregadas_count'] }} entregas</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p style="color: #999; text-align: center; padding: 2rem 0;">Sin datos</p>
            @endif
        </div>
    </div>
</div>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}
</style>
@endsection
