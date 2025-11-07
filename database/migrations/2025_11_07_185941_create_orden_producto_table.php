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
    Schema::create('orden_producto', function (Blueprint $table) {
    // Referencia explícita a la tabla 'ordenes' (plural en español)
    $table->foreignId('orden_id')->constrained('ordenes')->onDelete('cascade');
    $table->foreignId('producto_id')->constrained()->onDelete('cascade');
    $table->integer('cantidad');
    $table->decimal('precio_unitario', 8, 2); // Precio al momento de la orden
    $table->primary(['orden_id', 'producto_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_producto');
    }
};
