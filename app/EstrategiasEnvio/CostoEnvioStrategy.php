<?php

namespace App\EstrategiasEnvio;

// Interfaz Strategy: Define el método que todas las estrategias deben tener.
interface CostoEnvioStrategy
{
    /**
     * Calcula el costo de envío basado en la distancia y el peso.
     * @param float $distanciaKm La distancia de la entrega en kilómetros.
     * @param float $pesoKg El peso total de la orden en kilogramos.
     * @return float El costo final de envío.
     */
    public function calcularCosto(float $distanciaKm, float $pesoKg): float;
}