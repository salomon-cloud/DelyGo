@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Historial de Entregas</h1>
            <p class="text-gray-600 mt-1">Entregas completadas y canceladas</p>
        </div>

        @if($ordenes->isEmpty())
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-800">Sin entregas registradas</h3>
                <p class="text-gray-600 mt-2">Aún no has completado ninguna entrega.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Orden</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Cliente</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Restaurante</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Fecha</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Total</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Estado</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Calificación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ordenes as $orden)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">
                                <span class="font-medium text-gray-900">#{{ $orden->id }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $orden->cliente->name }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $orden->restaurante->nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $orden->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                ${{ number_format($orden->total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($orden->estado === 'entregada') bg-green-100 text-green-800
                                    @elseif($orden->estado === 'cancelada') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($orden->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($orden->rating)
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 @if($i <= $orden->rating->puntuacion) text-yellow-400 @else text-gray-300 @endif" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Sin calificar</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $ordenes->links() }}
            </div>
        @endif

        <!-- Botones de navegación -->
        <div class="mt-8 flex gap-3">
            <a href="{{ route('repartidor.ordenes') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Mis Entregas
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
