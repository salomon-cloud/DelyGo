@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <!-- Éxito -->
        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">¡Pago Exitoso!</h1>
            <p class="text-gray-600">Tu transacción ha sido procesada correctamente.</p>
        </div>

        <!-- Información de la transacción -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Detalles de la Transacción</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between p-3 bg-gray-50 rounded">
                    <span class="text-gray-700">ID de Transacción:</span>
                    <span class="font-mono font-medium text-gray-800">{{ $transaccion_id }}</span>
                </div>
                <div class="flex justify-between p-3 bg-gray-50 rounded">
                    <span class="text-gray-700">Estado:</span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm font-medium">Aprobado</span>
                </div>
                <div class="flex justify-between p-3 bg-gray-50 rounded">
                    <span class="text-gray-700">Fecha:</span>
                    <span class="font-medium text-gray-800">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Próximos pasos -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="font-bold text-blue-800 mb-3">Próximos pasos</h3>
            <ul class="space-y-2 text-blue-700">
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Tu orden ha sido registrada</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>El restaurante comenzará a preparar tu orden</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Recibirás notificaciones sobre el estado de tu pedido</span>
                </li>
            </ul>
        </div>

        <!-- Botones de acción -->
        <div class="flex gap-3">
            <a href="{{ route('cliente.ordenes.index') }}" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg text-center hover:bg-blue-700 transition font-medium">
                Ver Mis Órdenes
            </a>
            <a href="{{ route('cliente.orden.create') }}" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg text-center hover:bg-green-700 transition font-medium">
                Hacer Otro Pedido
            </a>
        </div>
    </div>
</div>
@endsection
