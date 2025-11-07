<?php

// app/Models/Orden.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\EstadosOrden\EstadoOrden; 
use App\EstadosOrden\Recibida; // Importa los estados concretos

class Orden extends Model
{
    // Nombre explícito de la tabla para coincidir con la migración (español)
    protected $table = 'ordenes';
    protected $fillable = ['cliente_id', 'restaurante_id', 'repartidor_id', 'estado', 'total', 'direccion_entrega'];

    // Mapeo de estados a clases concretas
    protected $estadosMap = [
        'recibida' => Recibida::class,
        // 'preparando' => Preparando::class, 
        // 'en_camino' => EnCamino::class,
        // ... otros estados
    ];

    // Propiedad para el objeto Estado actual
    protected ?EstadoOrden $estadoActual = null;

    // Método para obtener el objeto Estado actual (Contexto)
    public function getEstadoObjeto(): EstadoOrden
    {
        // Si no se ha inicializado o ha cambiado el estado en la DB, inicializa
        if ($this->estadoActual === null || $this->estadoActual->obtenerNombreEstado() !== $this->estado) {
            $claseEstado = $this->estadosMap[$this->estado] ?? Recibida::class; // Fallback
            $this->estadoActual = new $claseEstado();
        }
        return $this->estadoActual;
    }

    // Método principal para realizar una transición
    public function transicionarA(string $nuevoEstado): void
    {
        // Delega la lógica de transición al objeto de estado actual
        $this->getEstadoObjeto()->manejarTransicion($this, $nuevoEstado);
        $this->save(); // Guarda el cambio de estado en la DB

        // Disparar Evento (Patrón Observer - Ver Paso 5)
        // event(new \App\Events\EstadoOrdenCambio($this)); 
    }

    // Relaciones...
    public function cliente() { return $this->belongsTo(User::class, 'cliente_id'); }
    // ... otras relaciones...
}