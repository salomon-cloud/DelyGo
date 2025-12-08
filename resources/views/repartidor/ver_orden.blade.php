@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Orden #{{ $orden->id }}</h1>
            <p class="text-gray-600 mt-1">Detalles de la entrega</p>
        </div>

        <!-- Estado y cliente -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-medium text-gray-700 mb-2">Estado</h3>
                <p class="text-2xl font-bold
                    @if($orden->estado === 'en_camino') text-blue-600
                    @elseif($orden->estado === 'entregada') text-green-600
                    @else text-yellow-600
                    @endif">
                    {{ ucfirst($orden->estado) }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-medium text-gray-700 mb-2">Total</h3>
                <p class="text-2xl font-bold text-gray-800">${{ number_format($orden->total, 2) }}</p>
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h3 class="font-bold text-lg text-gray-800 mb-3">Información del Cliente</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nombre</p>
                    <p class="text-lg font-medium text-gray-800">{{ $orden->cliente->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-lg font-medium text-gray-800">{{ $orden->cliente->email }}</p>
                </div>
            </div>
        </div>

        <!-- Dirección de entrega -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h3 class="font-bold text-lg text-gray-800 mb-3">Dirección de Entrega</h3>
            <p class="text-lg text-gray-700">{{ $orden->direccion_entrega }}</p>
        </div>

        <!-- Restaurante -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h3 class="font-bold text-lg text-gray-800 mb-2">Restaurante</h3>
            <p class="text-lg text-gray-700">{{ $orden->restaurante->nombre }}</p>
        </div>

        <!-- Productos -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h3 class="font-bold text-lg text-gray-800 mb-3">Productos</h3>
            <div class="space-y-2">
                @foreach($orden->productos as $p)
                <div class="flex justify-between p-2 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium text-gray-800">{{ $p->nombre }}</p>
                        <p class="text-sm text-gray-600">Cantidad: {{ $p->pivot->cantidad }}</p>
                    </div>
                    <p class="font-medium text-gray-800">${{ number_format($p->pivot->precio_unitario * $p->pivot->cantidad, 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Actualizar estado -->
        @if($orden->estado !== 'entregada' && $orden->estado !== 'cancelada')
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h3 class="font-bold text-lg text-gray-800 mb-3">Actualizar Estado</h3>
            <form method="POST" action="{{ route('repartidor.ordenes.estado', $orden->id) }}" class="flex gap-2">
                @csrf
                <select name="nuevo_estado" class="flex-1 px-3 py-2 border border-gray-300 rounded">
                    <option value="en_camino" @if($orden->estado === 'en_camino') selected @endif>En Camino</option>
                    <option value="entregada">Entregada</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
                    Confirmar
                </button>
            </form>
        </div>
        @endif

        <!-- Botones de navegación -->
        <div class="flex gap-3">
            <a href="{{ route('repartidor.ordenes') }}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-center hover:bg-blue-700 transition font-medium">
                Volver a Mis Entregas
            </a>
            <a href="{{ url('/dashboard') }}" class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg text-center hover:bg-gray-600 transition font-medium">
                Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
