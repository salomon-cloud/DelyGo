<?php

namespace App\EstrategiasEnvio;

class EnvioPremium implements CostoEnvioStrategy // Estrategia Concreta 2
{
    public function calcularCosto(float $distanciaKm, float $pesoKg): float
    {
        // Lógica de Envío Premium: Tarifa plana alta
        return 15.00; 
    }
}