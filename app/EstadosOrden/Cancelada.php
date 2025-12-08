<?php
namespace App\EstadosOrden;

use App\Models\Orden;
use InvalidArgumentException;

class Cancelada implements EstadoOrden
{
    public function manejarTransicion(Orden $orden, string $nuevoEstado): void
    {
        // 'cancelada' es un estado final: no permite transiciones
        throw new InvalidArgumentException("La orden está 'cancelada' y no puede cambiar a '{$nuevoEstado}'");
    }

    public function obtenerNombreEstado(): string
    {
        return 'cancelada';
    }
}
