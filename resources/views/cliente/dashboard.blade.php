@extends('layout')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h2 class="card-title">🍕 Mi Panel - Cliente</h2>
        <p style="color: #666; margin-top: 0.5rem;">Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <!-- Cards de Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="text-align: center; border-left: 4px solid var(--primary-color);">
            <h3 style="font-size: 2rem; color: var(--primary-color);">{{ $totalOrdenes }}</h3>
            <p style="margin: 0; color: #666;">Órdenes Total</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--info-color);">
            <h3 style="font-size: 2rem; color: var(--info-color);">{{ $ordenesActivas }}</h3>
            <p style="margin: 0; color: #666;">Órdenes Activas</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--success-color);">
            <h3 style="font-size: 2rem; color: var(--success-color);">{{ $ordenesEntregadas }}</h3>
            <p style="margin: 0; color: #666;">Entregadas</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--danger-color);">
            <h3 style="font-size: 2rem; color: var(--danger-color);">{{ $ordenesCanceladas }}</h3>
            <p style="margin: 0; color: #666;">Canceladas</p>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">⚡ Acciones Rápidas</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('cliente.orden.create') }}" class="btn btn-primary">+ Nueva Orden</a>
            <a href="{{ route('cliente.ordenes.index') }}" class="btn btn-secondary">Ver Mis Órdenes</a>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline">Perfil</a>
        </div>
    </div>

    <!-- Órdenes Activas -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">📦 Mis Órdenes Activas</h3>
        
        @if($ordenesActuales->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">ID</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Restaurante</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Estado</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Total</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Fecha</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenesActuales as $orden)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.75rem;">#{{ $orden->id }}</td>
                            <td style="padding: 0.75rem;">{{ $orden->restaurante->nombre }}</td>
                            <td style="padding: 0.75rem;">
                                <span class="estado-badge estado-{{ $orden->estado }}">
                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; font-weight: 600;">${{ number_format($orden->total, 2) }}</td>
                            <td style="padding: 0.75rem;">{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                            <td style="padding: 0.75rem;">
                                <a href="{{ route('cliente.ordenes.show', $orden) }}" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 2rem; color: #999;">
                <p>No tienes órdenes activas</p>
                <a href="{{ route('cliente.orden.create') }}" class="btn btn-primary" style="margin-top: 1rem;">Crear tu primera orden</a>
            </div>
        @endif
    </div>

    <!-- Últimas Órdenes -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm);">
        <h3 style="margin-top: 0;">📋 Últimas Órdenes</h3>
        
        @if($ultimasOrdenes->count() > 0)
            <div style="display: grid; gap: 1rem;">
                @foreach($ultimasOrdenes as $orden)
                <div style="border: 1px solid #eee; padding: 1rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="margin: 0 0 0.5rem 0; font-weight: 600;">#{{ $orden->id }} - {{ $orden->restaurante->nombre }}</p>
                        <p style="margin: 0 0 0.25rem 0; color: #666; font-size: 0.9rem;">
                            <span class="estado-badge estado-{{ $orden->estado }}" style="padding: 0.25rem 0.75rem; font-size: 0.85rem;">
                                {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                            </span>
                        </p>
                        <p style="margin: 0; color: #999; font-size: 0.85rem;">{{ $orden->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0 0 0.5rem 0; font-weight: 600; font-size: 1.2rem;">${{ number_format($orden->total, 2) }}</p>
                        <a href="{{ route('cliente.ordenes.show', $orden) }}" class="btn btn-small btn-secondary">Ver Detalles</a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 2rem;">No hay órdenes anteriores</p>
        @endif
    </div>
</div>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}
</style>
@endsection
