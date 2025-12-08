@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Mis Entregas</h1>
            <p class="text-gray-600 mt-1">Órdenes asignadas para repartir</p>
        </div>

        @if($ordenes->isEmpty())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <svg class="w-16 h-16 mx-auto mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-blue-800">¡Sin órdenes pendientes!</h3>
                <p class="text-blue-700 mt-2">Todas las órdenes han sido entregadas o canceladas.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($ordenes as $orden)
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 @if($orden->estado === 'en_camino') border-blue-500 @else border-yellow-500 @endif">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Orden #{{ $orden->id }}</h3>
                            <p class="text-sm text-gray-600">{{ $orden->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($orden->estado === 'en_camino') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($orden->estado) }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="text-sm">
                            <strong class="text-gray-700">Cliente:</strong>
                            <p class="text-gray-600">{{ $orden->cliente->name }}</p>
                        </div>
                        <div class="text-sm">
                            <strong class="text-gray-700">Restaurante:</strong>
                            <p class="text-gray-600">{{ $orden->restaurante->nombre }}</p>
                        </div>
                        <div class="text-sm">
                            <strong class="text-gray-700">Dirección:</strong>
                            <p class="text-gray-600">{{ $orden->direccion_entrega }}</p>
                        </div>
                        <div class="text-sm">
                            <strong class="text-gray-700">Total:</strong>
                            <p class="text-gray-600">${{ number_format($orden->total, 2) }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('repartidor.ordenes.ver', $orden->id) }}" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded text-center text-sm hover:bg-blue-700 transition">
                            Ver Detalle
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $ordenes->links() }}
            </div>
        @endif

        <!-- Botones adicionales -->
        <div class="mt-8 flex gap-3">
            <a href="{{ route('repartidor.historial') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Ver Historial
            </a>
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
