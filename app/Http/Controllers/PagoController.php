<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePagoRequest;

class PagoController extends Controller
{
    /**
     * Muestra la página de checkout/resumen del pago.
     * En futuro, aquí iría la integración con Stripe, PayPal, etc.
     */
    public function checkout(Request $request)
    {
        $cliente = Auth::user();
        if (! $cliente) {
            return redirect()->route('login');
        }

        // Datos simulados del carrito/orden
        $total = $request->total ?? 0;
        $items = $request->items ?? 0; // cantidad de items

        return view('pago.checkout', [
            'total' => $total,
            'items' => $items,
        ]);
    }

    /**
     * Procesa el pago (simulado).
     * En producción: integrar con Stripe API, handle webhooks, etc.
     */
    public function procesar(StorePagoRequest $request)
    {
        // Validación realizada por StorePagoRequest
        $validated = $request->validated();

        $cliente = Auth::user();
        if (! $cliente) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }

        // Simular procesamiento (en producción: llamar a API de Stripe, etc.)
        \Log::info("[Pago] Usuario {$cliente->id} procesó pago de \${$request->monto} via {$request->metodo_pago}");

        // Devolver respuesta exitosa (en producción: verificar webhook de Stripe, etc.)
        return response()->json([
            'success' => true,
            'message' => 'Pago procesado exitosamente (simulado)',
            'transaccion_id' => 'TXN-' . uniqid(),
        ]);
    }

    /**
     * Confirma el pago después de procesarlo.
     * Redirige a la orden o resumen.
     */
    public function confirmacion($transaccion_id)
    {
        return view('pago.confirmacion', [
            'transaccion_id' => $transaccion_id,
        ]);
    }
}
