<?php

namespace App\EstrategiasEnvio;

class EnvioEstandar implements CostoEnvioStrategy // Estrategia Concreta 1
{
    public function calcularCosto(float $distanciaKm, float $pesoKg): float
    {
        // Lógica de Envío Estándar: Tarifa base + Costo por kilómetro
        $costoBase = 5.00;
        $costoPorKm = 0.50;
        return $costoBase + ($distanciaKm * $costoPorKm);
    }
}