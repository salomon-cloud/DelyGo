<?php

namespace App\Listeners;

use App\Events\EstadoOrdenCambio;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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

        // Enviar correo (ejemplo)
        \Mail::to($orden->cliente->email)->send(new NotificacionEstadoOrden($orden));

        // Log de la notificación (ejemplo)
        \Log::info("Notificado al cliente {$orden->cliente->id} sobre el estado '{$orden->estado}' de la orden #{$orden->id}");

        // Lógica para enviar notificación por Laravel Echo (real-time, ver Paso 8)

    }
}
