<?php
// app/EstadosOrden/Recibida.php
namespace App\EstadosOrden;

use App\Models\Orden;
use InvalidArgumentException;

class Recibida implements EstadoOrden
{
    public function manejarTransicion(Orden $orden, string $nuevoEstado): void
    {
        // Solo puede pasar a 'preparando' o 'cancelada'
        if (!in_array($nuevoEstado, ['preparando', 'cancelada'])) {
            throw new InvalidArgumentException("TransiciÃ³n invÃ¡lida desde 'recibida' a '{$nuevoEstado}'");
        }

        // Asigna el nuevo estado (se persiste en el modelo)
        $orden->estado = $nuevoEstado;

        // LÃ³gica adicional para el cambio (ej. enviar notificaciÃ³n)
    }

    public function obtenerNombreEstado(): string
    {
        return 'recibida';
    }
}

// Se repite esta lÃ³gica para Preparando, EnCamino, Entregada, Cancelada,
// definiendo las transiciones vÃ¡lidas para cada una.
// Ejemplo para EnCamino: solo puede pasar a 'entregada' o 'cancelada'


