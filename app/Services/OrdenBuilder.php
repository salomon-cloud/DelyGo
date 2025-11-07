<?php

namespace App\Services;

use App\Models\Orden;
use App\Models\Producto;
use App\EstrategiasEnvio\CostoEnvioStrategy; // 🎯 Importar la interfaz Strategy
use App\Services\CalculadorEnvio;          // 🎯 Importar el Contexto Strategy
use Exception; // Para manejar excepciones como la mezcla de restaurantes

class OrdenBuilder
{
    protected Orden $orden;
    protected array $productos;
    protected float $costoEnvio = 0.0; // Almacena el costo de envío calculado

    /**
     * Constructor del Builder. Ahora requiere los datos iniciales y la estrategia de envío.
     * * @param int $clienteId El ID del cliente que realiza el pedido.
     * @param string $direccionEntrega La dirección a donde se enviará.
     * @param float $distanciaKm Distancia simulada o real de la entrega.
     * @param CostoEnvioStrategy $estrategiaEnvio La estrategia de cálculo de envío elegida.
     */
    public function __construct(int $clienteId, string $direccionEntrega, float $distanciaKm, CostoEnvioStrategy $estrategiaEnvio)
    {
        // Paso 1: Inicializar la estructura de la orden
        $this->orden = new Orden([
            'cliente_id' => $clienteId,
            'estado' => 'recibida',
            'direccion_entrega' => $direccionEntrega,
            'total' => 0, // Se actualizará al final
        ]);
        $this->productos = [];
        
        // 🎯 Uso del Patrón Strategy: Calcular el costo de envío
        // Asumimos un peso simulado de 1.0 kg para el cálculo.
        $calculador = new CalculadorEnvio($distanciaKm, 1.0, $estrategiaEnvio);
        $this->costoEnvio = $calculador->calcularCosto();
    }
    
    // Paso 2: Construir/añadir productos (se mantiene igual)
    public function agregarProducto(Producto $producto, int $cantidad): self
    {
        if (empty($this->orden->restaurante_id)) {
             // Asigna el restaurante con el primer producto añadido
            $this->orden->restaurante_id = $producto->restaurante_id;
        } elseif ($this->orden->restaurante_id !== $producto->restaurante_id) {
            throw new Exception("ERROR: No se pueden mezclar productos de diferentes restaurantes.");
        }

        $this->productos[] = [
            'producto_id' => $producto->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $producto->precio,
        ];

        return $this; // Retorna la instancia para encadenar llamadas
    }
    
    // Paso 3: Calcular el total (Modificado para incluir costo de envío)
    protected function calcularTotal(): float
    {
        $subtotal = 0;
        foreach ($this->productos as $item) {
            $subtotal += $item['cantidad'] * $item['precio_unitario'];
        }
        
        // 🎯 Sumar el costo de envío calculado por el Strategy
        return $subtotal + $this->costoEnvio;
    }
    
    // Paso 4: Finalizar y obtener la orden (Persistir) - Se mantiene igual
    public function obtenerOrden(): Orden
    {
        if (empty($this->productos)) {
            throw new Exception("ERROR: La orden no puede estar vacía.");
        }
        
        $this->orden->total = $this->calcularTotal();
        $this->orden->save(); // Guardar la orden principal

        // ... (Guardar productos en la tabla pivote, se mantiene igual) ...
        $itemsPivot = collect($this->productos)->mapWithKeys(function ($item) {
             return [$item['producto_id'] => ['cantidad' => $item['cantidad'], 'precio_unitario' => $item['precio_unitario']]];
        })->toArray();

        $this->orden->productos()->attach($itemsPivot); 

        // Disparar evento inicial (Patrón Observer)
        event(new \App\Events\EstadoOrdenCambio($this->orden));

        return $this->orden;
    }
}