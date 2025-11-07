<?php

namespace App\Events;

// Usamos el modelo Orden, necesitamos importarlo
use App\Models\Orden; 
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // 🎯 NECESARIO para el Patrón Observer (Real-time)
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstadoOrdenCambio implements ShouldBroadcast // 1. Implementar la interfaz para Broadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // 2. Definir correctamente la propiedad pública con el tipo Orden
    public Orden $orden; 

    public function __construct(Orden $orden)
    {
        $this->orden = $orden;
    }

    /**
     * Define los canales privados donde se escuchará el evento (tracking).
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // 🎯 Patrón Observer: Cada cliente escucha en un canal privado ÚNICO.
        // Solo el cliente con este ID recibirá la notificación de su orden.
        return [
            new PrivateChannel('orden.' . $this->orden->cliente_id),
        ];
    }
    
    /**
     * Define los datos que se enviarán al frontend (Vue.js) cuando ocurra el evento.
     * Esto reemplaza a 'broadcastOn' en Laravel 11/12 si usas solo los datos.
     */
    public function broadcastWith(): array
    {
        return [
            'orden_id' => $this->orden->id,
            'nuevo_estado' => $this->orden->estado, // El estado actual
            'mensaje' => "Tu orden #{$this->orden->id} está ahora: " . $this->orden->estado,
        ];
    }
}