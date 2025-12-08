<?php

use Illuminate\Database\Migrations\Migration;

// Esta migración está duplicada. La migración correcta es: 2025_11_07_185743_create_ratings_table.php
// Esta archivo puede ser eliminado.

return new class extends Migration
{
    public function up(): void
    {
        // No hacer nada - la tabla ya existe en la migración anterior
    }

    public function down(): void
    {
        // No hacer nada
    }
};

