<?php
namespace App\EstadosOrden;

use App\Models\Orden;
use InvalidArgumentException;

class EnCamino implements EstadoOrden
{
    public function manejarTransicion(Orden $orden, string $nuevoEstado): void
    {
        // Desde 'en_camino' solo puede pasar a 'entregada' o 'cancelada'
        if (! in_array($nuevoEstado, ['entregada', 'cancelada'])) {
            throw new InvalidArgumentException("Transición inválida desde 'en_camino' a '{$nuevoEstado}'");
        }

        $orden->estado = $nuevoEstado;
    }

    public function obtenerNombreEstado(): string
    {
        return 'en_camino';
    }
}
