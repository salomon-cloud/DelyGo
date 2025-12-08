@extends('layout')

@section('content')
<div>
    <h2 class="card-title">Panel de Administrador</h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="text-align: center;">
            <h3 style="font-size: 2rem; color: var(--primary-color);">{{ $totalRestaurantes }}</h3>
            <p>Restaurantes</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3 style="font-size: 2rem; color: var(--secondary-color);">{{ $totalProductos }}</h3>
            <p>Productos</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3 style="font-size: 2rem; color: var(--info-color);">{{ $totalOrdenes }}</h3>
            <p>Órdenes</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3 style="font-size: 2rem; color: var(--success-color);">{{ $totalRepartidores }}</h3>
            <p>Repartidores</p>
        </div>
    </div>

    <h3>Acciones Rápidas</h3>
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <a href="{{ route('admin.restaurantes') }}" class="btn btn-primary">Gestionar Restaurantes</a>
        <a href="{{ route('admin.productos') }}" class="btn btn-secondary">Gestionar Productos</a>
        <a href="{{ route('admin.asignacion') }}" class="btn btn-info">Asignar Repartidores</a>
    </div>

    <h3>Órdenes Recientes</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Restaurante</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ordenesRecientes as $orden)
                <tr>
                    <td>#{{ $orden->id }}</td>
                    <td>{{ $orden->cliente->name ?? 'N/A' }}</td>
                    <td>{{ $orden->restaurante->nombre ?? 'N/A' }}</td>
                    <td>
                        <span class="estado-badge estado-{{ $orden->estado }}">
                            {{ ucfirst($orden->estado) }}
                        </span>
                    </td>
                    <td>${{ number_format($orden->total, 2) }}</td>
                    <td>{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay órdenes</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
