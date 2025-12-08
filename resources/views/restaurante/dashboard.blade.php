@extends('layout')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h2 class="card-title">🍽️ Mi Panel - Restaurante</h2>
        <p style="color: #666; margin-top: 0.5rem;">{{ $restaurante->nombre }} | {{ auth()->user()->name }}</p>
    </div>

    <!-- Cards de Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="text-align: center; border-left: 4px solid var(--primary-color);">
            <h3 style="font-size: 2rem; color: var(--primary-color);">{{ $totalProductos }}</h3>
            <p style="margin: 0; color: #666;">Productos</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--info-color);">
            <h3 style="font-size: 2rem; color: var(--info-color);">{{ $ordenesPendientes }}</h3>
            <p style="margin: 0; color: #666;">Órdenes Pendientes</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--success-color);">
            <h3 style="font-size: 2rem; color: var(--success-color);">{{ $ordenesEntregadas }}</h3>
            <p style="margin: 0; color: #666;">Órdenes Entregadas</p>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--warning-color);">
            <h3 style="font-size: 2rem; color: var(--warning-color); text-align: right; padding-right: 1rem;">{{ $productosSinStock }}</h3>
            <p style="margin: 0; color: #666;">Sin Stock</p>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">⚡ Acciones Rápidas</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('productos.index') }}" class="btn btn-primary">Gestionar Productos</a>
            <a href="{{ route('restaurante.ordenes.pendientes') }}" class="btn btn-secondary">Órdenes Pendientes</a>
            <button onclick="openProductModal()" class="btn btn-success">+ Agregar Producto</button>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline">Perfil</a>
        </div>
    </div>

    <!-- Órdenes Pendientes de Preparar -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">👨‍🍳 Órdenes Pendientes de Preparar</h3>
        
        @if($ordenesPreparar->count() > 0)
            <div style="display: grid; gap: 1rem;">
                @foreach($ordenesPreparar as $orden)
                <div style="border: 2px solid var(--warning-color); padding: 1rem; border-radius: 6px; background: #fffbeb;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <div>
                            <p style="margin: 0 0 0.25rem 0; font-weight: 600; font-size: 1.1rem;">#{{ $orden->id }}</p>
                            <p style="margin: 0 0 0.5rem 0; color: #666; font-size: 0.9rem;">
                                👤 {{ $orden->cliente->name }}
                            </p>
                        </div>
                        <span class="estado-badge estado-{{ $orden->estado }}" style="padding: 0.5rem 0.75rem;">
                            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                        </span>
                    </div>

                    <div style="background: white; padding: 0.75rem; border-radius: 4px; margin-bottom: 0.75rem; font-size: 0.9rem;">
                        <p style="margin: 0 0 0.25rem 0;"><strong>Items:</strong></p>
                        <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                            @foreach($orden->items as $item)
                            <li style="margin: 0.25rem 0;">{{ $item['nombre'] }} x {{ $item['cantidad'] }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div style="background: #f9fafb; padding: 0.75rem; border-radius: 4px; margin-bottom: 0.75rem; border-left: 3px solid var(--primary-color);">
                        <p style="margin: 0 0 0.25rem 0;"><strong>📍 Destino:</strong> {{ Str::limit($orden->direccion_entrega, 60) }}</p>
                        <p style="margin: 0 0 0.25rem 0;"><strong>💰 Total:</strong> ${{ number_format($orden->total, 2) }}</p>
                        <p style="margin: 0;"><strong>🕐 Creada:</strong> {{ $orden->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <a href="{{ route('restaurante.ordenes.pendientes') }}?orden={{ $orden->id }}" class="btn btn-primary" style="flex: 1; text-align: center;">Ver Detalles</a>
                        
                        <form action="{{ route('restaurante.ordenes.cambiarEstado', $orden) }}" method="POST" style="display: inline; flex: 1;">
                            @csrf
                            <input type="hidden" name="nuevo_estado" value="preparando">
                            <button type="submit" class="btn btn-warning" style="width: 100%; cursor: pointer;">👨‍🍳 Comenzar Preparación</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 2rem; color: #999; background: #f9fafb; border-radius: 6px;">
                <p style="margin: 0; font-size: 1.1rem;">✓ No hay órdenes pendientes</p>
                <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">Todas las órdenes están en preparación o entregadas</p>
            </div>
        @endif
    </div>

    <!-- Órdenes en Preparación -->
    @if($ordenesEnPrep->count() > 0)
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h3 style="margin-top: 0;">👨‍🍳 Órdenes en Preparación</h3>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">ID</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Cliente</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Items</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Total</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Tiempo</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordenesEnPrep as $orden)
                    <tr style="border-bottom: 1px solid #eee; background: #fafafa;">
                        <td style="padding: 0.75rem;"><strong>#{{ $orden->id }}</strong></td>
                        <td style="padding: 0.75rem;">{{ $orden->cliente->name }}</td>
                        <td style="padding: 0.75rem; font-size: 0.9rem;">{{ count($orden->items) }} items</td>
                        <td style="padding: 0.75rem; font-weight: 600;">${{ number_format($orden->total, 2) }}</td>
                        <td style="padding: 0.75rem; font-size: 0.9rem;">{{ $orden->created_at->diffForHumans() }}</td>
                        <td style="padding: 0.75rem;">
                            <form action="{{ route('restaurante.ordenes.cambiarEstado', $orden) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="nuevo_estado" value="en_camino">
                                <button type="submit" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.9rem;">✓ Listo</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Productos Disponibles -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0;">📋 Mis Productos ({{ $totalProductos }})</h3>
            <a href="{{ route('productos.index') }}" class="btn btn-small btn-primary">Ver Todo</a>
        </div>
        
        @if($productos->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                @foreach($productos->take(4) as $producto)
                <div style="border: 1px solid #eee; padding: 1rem; border-radius: 6px; display: flex; flex-direction: column;">
                    <div style="margin-bottom: 0.75rem;">
                        <p style="margin: 0 0 0.25rem 0; font-weight: 600;">{{ $producto->nombre }}</p>
                        <p style="margin: 0 0 0.5rem 0; color: #666; font-size: 0.9rem;">{{ Str::limit($producto->descripcion, 50) }}</p>
                        <p style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--primary-color);">${{ number_format($producto->precio, 2) }}</p>
                    </div>
                    
                    <div style="background: #f9fafb; padding: 0.75rem; border-radius: 4px; margin-bottom: 0.75rem; font-size: 0.9rem;">
                        <p style="margin: 0;">
                            @if($producto->disponible)
                                <span style="color: var(--success-color); font-weight: 600;">✓ Disponible</span>
                            @else
                                <span style="color: var(--danger-color); font-weight: 600;">✗ No Disponible</span>
                            @endif
                        </p>
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem; margin-top: auto;">
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-small btn-secondary" style="flex: 1;">Editar</a>
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="flex: 1;" onsubmit="return confirm('¿Estás seguro?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-small btn-danger" style="width: 100%; margin: 0;">Eliminar</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 2rem; color: #999;">
                <p>No tienes productos cargados</p>
                <button onclick="openProductModal()" class="btn btn-primary" style="margin-top: 1rem;">+ Agregar tu primer producto</button>
            </div>
        @endif
    </div>
</div>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}
</style>

<script>
function openProductModal() {
    alert('Redirigiendo a crear producto...');
    window.location.href = '{{ route("productos.index") }}';
}
</script>
@endsection
