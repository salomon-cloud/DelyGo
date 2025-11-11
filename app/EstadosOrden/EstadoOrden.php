<?php

namespace App\EstadosOrden;
use App\Models\Orden;

interface EstadoOrden
{
    // Método para el cambio de estado (transición)
    public function manejarTransicion(Orden $orden, string $nuevoEstado): void;

    // Método para obtener el nombre legible del estado (opcional)
    public function obtenerNombreEstado(): string;
}
