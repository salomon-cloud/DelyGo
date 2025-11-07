<?php

namespace App\Services;

use App\EstrategiasEnvio\CostoEnvioStrategy;
use App\EstrategiasEnvio\EnvioEstandar;

class CalculadorEnvio // El Contexto del Patrón Strategy
{
    protected CostoEnvioStrategy $estrategia;
    protected float $distanciaKm;
    protected float $pesoKg;

    // Se inyecta la estrategia a usar en el constructor (o se usa una por defecto)
    public function __construct(float $distanciaKm, float $pesoKg, ?CostoEnvioStrategy $estrategia = null)
    {
        $this->distanciaKm = $distanciaKm;
        $this->pesoKg = $pesoKg;
        $this->estrategia = $estrategia ?? new EnvioEstandar(); // Estrategia por defecto
    }

    // Permite cambiar la estrategia en tiempo de ejecución
    public function setEstrategia(CostoEnvioStrategy $estrategia): void
    {
        $this->estrategia = $estrategia;
    }

    // Delega el cálculo del costo a la estrategia seleccionada actualmente
    public function calcularCosto(): float
    {
        return $this->estrategia->calcularCosto($this->distanciaKm, $this->pesoKg);
    }
}
