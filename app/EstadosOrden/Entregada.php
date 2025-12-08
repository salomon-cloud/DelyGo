<?php
namespace App\EstadosOrden;

use App\Models\Orden;
use InvalidArgumentException;

class Entregada implements EstadoOrden
{
    public function manejarTransicion(Orden $orden, string $nuevoEstado): void
    {
        // 'entregada' es un estado final: no permite transiciones
        throw new InvalidArgumentException("La orden ya está 'entregada' y no puede cambiar a '{$nuevoEstado}'");
    }

    public function obtenerNombreEstado(): string
    {
        return 'entregada';
    }
}
