<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('ordenes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade'); // Cliente que ordena
    $table->foreignId('restaurante_id')->constrained()->onDelete('cascade'); // Restaurante de la orden
    $table->foreignId('repartidor_id')->nullable()->constrained('users')->onDelete('set null'); // Repartidor asignado (opcional al inicio)

    // El estado de la orden (Patrón State - Implementación inicial)
    // 'recibida' -> 'preparando' -> 'en_camino' -> 'entregada' -> 'cancelada'
    $table->enum('estado', ['recibida', 'preparando', 'en_camino', 'entregada', 'cancelada'])->default('recibida');

    $table->decimal('total', 8, 2);
    $table->string('direccion_entrega');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
