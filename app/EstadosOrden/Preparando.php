<?php
namespace App\EstadosOrden;

use App\Models\Orden;
use InvalidArgumentException;

class Preparando implements EstadoOrden
{
    public function manejarTransicion(Orden $orden, string $nuevoEstado): void
    {
        // Desde 'preparando' solo se puede pasar a 'en_camino' o 'cancelada'
        if (! in_array($nuevoEstado, ['en_camino', 'cancelada'])) {
            throw new InvalidArgumentException("Transición inválida desde 'preparando' a '{$nuevoEstado}'");
        }

        $orden->estado = $nuevoEstado;
    }

    public function obtenerNombreEstado(): string
    {
        return 'preparando';
    }
}
