<?php

namespace App\Listeners;

use App\Events\EstadoOrdenCambio;
use App\Mail\OrdenEstadoCambio;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificarClienteEstadoOrden
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EstadoOrdenCambio $event): void
    {
        $orden = $event->orden;
        $cliente = $orden->cliente;

        if (! $cliente) {
            return;
        }

        // Log de la notificación
        Log::info("[Notificación] Cliente {$cliente->id} ({$cliente->email}): Orden #{$orden->id} está en estado '{$orden->estado}'");

        // Enviar email al cliente
        try {
            Mail::to($cliente->email)->send(new OrdenEstadoCambio($orden));
            Log::info("[Email Enviado] Notificación de cambio de estado enviada a {$cliente->email}");
        } catch (\Exception $e) {
            Log::error("[Email Error] Fallo al enviar notificación a {$cliente->email}: " . $e->getMessage());
        }
    }
}
