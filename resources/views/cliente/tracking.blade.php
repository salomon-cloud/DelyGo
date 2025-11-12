@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Tracking de la orden</h1>

    @if(empty($orden))
        <div class="p-4 bg-yellow-50 rounded">Orden no encontrada.</div>
    @else
        <div class="space-y-3">
            <div class="p-4 bg-white rounded shadow">
                <h3 class="text-lg font-semibold">Orden #{{ $orden->id }}</h3>
                <p class="text-sm text-gray-600">Estado: <strong>{{ $orden->estado }}</strong></p>
                <p class="text-sm text-gray-600">Cliente: {{ $orden->user->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">Dirección de entrega: {{ $orden->direccion_entrega ?? '---' }}</p>
                <p class="text-sm text-gray-600">Repartidor: {{ $orden->repartidor?->name ?? 'Sin asignar' }}</p>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <h4 class="font-medium">Productos</h4>
                <ul class="list-disc pl-5 mt-2">
                    @foreach($orden->productos ?? [] as $p)
                        <li>{{ $p->nombre ?? ($p['nombre'] ?? 'Producto') }} — cantidad: {{ $p->pivot->cantidad ?? ($p['cantidad'] ?? 1) }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <h4 class="font-medium">Mapa / Seguimiento</h4>
                <div class="mt-2 text-sm text-gray-600">(Aquí puedes agregar un mapa o enlace al servicio de tracking)</div>
                <div class="mt-3 h-40 bg-gray-100 flex items-center justify-center rounded">Mapa placeholder</div>
            </div>
        </div>
    @endif
</div>
@endsection
