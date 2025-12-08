@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">¿De qué restaurante quieres ordenar?</h1>
            <p class="text-gray-600">Selecciona un restaurante para ver su menú y hacer tu pedido</p>
        </div>

        @if($restaurantes->isEmpty())
            <div class="text-center py-12">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mx-auto max-w-md">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-yellow-800 mb-2">No hay restaurantes disponibles</h3>
                    <p class="text-yellow-700">Actualmente no hay restaurantes registrados en el sistema.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($restaurantes as $restaurante)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-200">
                    <!-- Imagen placeholder -->
                    <div class="h-48 bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                        <div class="text-white text-center">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-lg font-semibold">{{ $restaurante->nombre }}</div>
                        </div>
                    </div>
                    
                    <!-- Contenido de la tarjeta -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $restaurante->nombre }}</h3>
                        
                        @if($restaurante->descripcion)
                            <p class="text-gray-600 mb-3 text-sm line-clamp-2">{{ $restaurante->descripcion }}</p>
                        @endif
                        
                        <div class="flex items-center text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm">{{ Str::limit($restaurante->direccion, 50) }}</span>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Abierto</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">{{ $restaurante->productos->count() ?? 0 }}</span> productos
                            </div>
                        </div>
                        
                        <!-- Botón para seleccionar -->
                        <a href="{{ route('cliente.orden.create.restaurante', $restaurante->id) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 text-center block">
                            Ver Menú y Ordenar
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
        
        <!-- Botón para volver -->
        <div class="text-center mt-8">
            <a href="{{ url('/dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
