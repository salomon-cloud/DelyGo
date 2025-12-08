@extends('layout')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h2 class="card-title">🚗 Mi Panel - Repartidor</h2>
        <p style="color: #666; margin-top: 0.5rem;">Bienvenido, {{ auth()->user()->name }}</p>
    </div>

    <!-- Cards de Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="text-align: center; border-left: 4px solid var(--info-color);">
            <h3 style="font-size: 2rem; color: var(--info-color);">{{ $ordenesAsignadas }}</h3>
            <p style="margin: 0; color: #666;">Por Entregar</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--warning-color);">
            <h3 style="font-size: 2rem; color: var(--warning-color);">{{ $ordenesEnCamino }}</h3>
            <p style="margin: 0; color: #666;">En Camino</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--success-color);">
            <h3 style="font-size: 2rem; color: var(--success-color);">{{ $ordenesEntregadas }}</h3>
            <p style="margin: 0; color: #666;">Entregadas Hoy</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid #9CA3AF;">
            <h3 style="font-size: 2rem; color: #9CA3AF;">{{ $totalEntregadas }}</h3>
            <p style="margin: 0; color: #666;">Total Entregadas</p>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">⚡ Acciones Rápidas</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('repartidor.ordenes') }}" class="btn btn-primary">Ver Mis Órdenes</a>
            <a href="{{ route('repartidor.historial') }}" class="btn btn-secondary">Historial</a>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline">Perfil</a>
        </div>
    </div>

    <!-- Órdenes Asignadas Pendientes -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">📦 Órdenes Asignadas</h3>
        
        @if($ordenesPendientes->count() > 0)
            <div style="display: grid; gap: 1rem;">
                @foreach($ordenesPendientes as $orden)
                <div style="border: 1px solid #eee; padding: 1rem; border-radius: 6px; background: #fafafa;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <div>
                            <p style="margin: 0 0 0.25rem 0; font-weight: 600; font-size: 1.1rem;">#{{ $orden->id }} - {{ $orden->restaurante->nombre }}</p>
                            <p style="margin: 0 0 0.5rem 0; color: #666; font-size: 0.9rem;">
                                👤 {{ $orden->cliente->name }}
                            </p>
                        </div>
                        <span class="estado-badge estado-{{ $orden->estado }}" style="padding: 0.5rem 0.75rem;">
                            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                        </span>
                    </div>

                    <div style="background: white; padding: 0.75rem; border-radius: 4px; margin-bottom: 0.75rem; font-size: 0.9rem;">
                        <p style="margin: 0 0 0.25rem 0;"><strong>📍 Destino:</strong> {{ $orden->direccion_entrega }}</p>
                        <p style="margin: 0 0 0.25rem 0;"><strong>💰 Total:</strong> ${{ number_format($orden->total, 2) }}</p>
                        <p style="margin: 0;"><strong>🕐 Creada:</strong> {{ $orden->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <a href="{{ route('repartidor.ordenes.ver', $orden) }}" class="btn btn-primary" style="flex: 1; text-align: center;">Ver Detalles</a>
                        
                        @if($orden->estado !== 'cancelada' && $orden->estado !== 'entregada')
                            <form action="{{ route('repartidor.ordenes.estado', $orden) }}" method="POST" style="display: inline; flex: 1;">
                                @csrf
                                <input type="hidden" name="nuevo_estado" value="{{ $orden->estado === 'recibida' ? 'en_camino' : 'entregada' }}">
                                <button type="submit" class="btn {{ $orden->estado === 'recibida' ? 'btn-warning' : 'btn-success' }}" style="width: 100%;">
                                    {{ $orden->estado === 'recibida' ? '🚗 Salir a Entregar' : '✓ Marcar Entregada' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 2rem; color: #999;">
                <p>No tienes órdenes asignadas en este momento</p>
                <p style="font-size: 0.9rem; margin-top: 0.5rem;">El administrador te asignará nuevas órdenes cuando estén disponibles</p>
            </div>
        @endif
    </div>

    <!-- Órdenes en Camino -->
    @if($ordenesEnCaminoList->count() > 0)
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">🚗 Órdenes en Camino</h3>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #fff3cd;">
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">ID</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Cliente</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Restaurante</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Destino</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Total</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordenesEnCaminoList as $orden)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 0.75rem;"><strong>#{{ $orden->id }}</strong></td>
                        <td style="padding: 0.75rem;">{{ $orden->cliente->name }}</td>
                        <td style="padding: 0.75rem;">{{ $orden->restaurante->nombre }}</td>
                        <td style="padding: 0.75rem; font-size: 0.9rem;">{{ Str::limit($orden->direccion_entrega, 30) }}</td>
                        <td style="padding: 0.75rem; font-weight: 600;">${{ number_format($orden->total, 2) }}</td>
                        <td style="padding: 0.75rem;">
                            <form action="{{ route('repartidor.ordenes.estado', $orden) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="nuevo_estado" value="entregada">
                                <button type="submit" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Entregar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Estadísticas del Día -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm);">
        <h3 style="margin-top: 0;">📊 Estadísticas de Hoy</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
            <div style="background: #f0f9ff; padding: 1rem; border-radius: 6px; text-align: center;">
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Órdenes Completadas</p>
                <p style="margin: 0.5rem 0 0 0; font-size: 1.5rem; font-weight: 600; color: var(--success-color);">{{ $ordenesEntregadasHoy }}</p>
            </div>
            <div style="background: #fef3c7; padding: 1rem; border-radius: 6px; text-align: center;">
                <p style="margin: 0; color: #666; font-size: 0.9rem;">En Progreso</p>
                <p style="margin: 0.5rem 0 0 0; font-size: 1.5rem; font-weight: 600; color: var(--warning-color);">{{ $ordenesEnCamino }}</p>
            </div>
            <div style="background: #f3e8ff; padding: 1rem; border-radius: 6px; text-align: center;">
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Promedio por Orden</p>
                <p style="margin: 0.5rem 0 0 0; font-size: 1.5rem; font-weight: 600; color: #8b5cf6;">
                    @if($ordenesEntregadasHoy > 0)
                        ${{ number_format($totalEntregasHoy / $ordenesEntregadasHoy, 2) }}
                    @else
                        $0.00
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
