@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Resumen del Pago</h1>
        </div>

        <!-- Resumen -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-700">Items:</span>
                <span class="font-medium">{{ $items ?? 0 }}</span>
            </div>
            <div class="flex justify-between items-center border-t pt-4">
                <span class="text-lg font-bold text-gray-800">Total a Pagar:</span>
                <span class="text-2xl font-bold text-green-600">${{ number_format($total ?? 0, 2) }}</span>
            </div>
        </div>

        <!-- Métodos de pago -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Método de Pago</h2>

            <form id="pago-form" method="POST" action="{{ route('pago.procesar') }}">
                @csrf

                <div class="space-y-4">
                    <!-- Tarjeta de crédito/débito -->
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="metodo_pago" value="tarjeta" checked class="mr-3">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">Tarjeta de Crédito / Débito</p>
                            <p class="text-sm text-gray-600">Visa, Mastercard, American Express</p>
                        </div>
                    </label>

                    <!-- Transferencia bancaria -->
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="metodo_pago" value="transferencia" class="mr-3">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">Transferencia Bancaria</p>
                            <p class="text-sm text-gray-600">Depósito directo a cuenta</p>
                        </div>
                    </label>

                    <!-- Efectivo -->
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="metodo_pago" value="efectivo" class="mr-3">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">Pago en Efectivo</p>
                            <p class="text-sm text-gray-600">Al momento de la entrega</p>
                        </div>
                    </label>
                </div>

                <!-- Campo oculto para monto -->
                <input type="hidden" name="monto" value="{{ $total ?? 0 }}">

                <!-- Botones -->
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Confirmar Pago
                    </button>
                    <a href="{{ route('cliente.orden.create') }}" class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 rounded-lg text-center hover:bg-gray-400 transition font-medium">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Aviso de seguridad -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <strong>Nota:</strong> Este es un sistema de pago simulado. En producción, se integraría con Stripe, PayPal u otro procesador de pagos certificado.
            </p>
        </div>
    </div>
</div>

<script>
    document.getElementById('pago-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const metodo = document.querySelector('input[name="metodo_pago"]:checked').value;
        console.log('Pago simulado con método:', metodo);
        
        // Simular procesamiento
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                monto: {{ $total ?? 0 }},
                metodo_pago: metodo,
            })
        }).then(r => r.json())
         .then(data => {
            if (data.success) {
                window.location.href = '{{ route("pago.confirmacion", "") }}/' + data.transaccion_id;
            } else {
                alert('Error: ' + data.message);
            }
         });
    });
</script>
@endsection
