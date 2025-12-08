<?php

namespace App\Services;

use App\Models\User;

class UserFactory
{
    
    // El "método de fábrica" para crear el tipo de usuario deseado.
    public static function crearUsuario(string $rol, array $datos): User
    {
        // Valida que el rol sea uno permitido
        if (! in_array($rol, ['cliente', 'restaurante', 'repartidor', 'admin'])) {
            throw new \InvalidArgumentException("Rol de usuario no válido.");
        }

        // Asigna el rol a los datos antes de la creación
        $datos['rol'] = $rol;

        // Retorna el nuevo usuario con el rol asignado
        return User::create($datos);
    }
}
