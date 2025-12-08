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
        // Restaurantes
        if (! Schema::hasTable('restaurantes')) {
            Schema::create('restaurantes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('direccion')->nullable();
                $table->string('telefono')->nullable();
                $table->timestamps();
            });
        }

        // Productos
        if (! Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurante_id')->constrained('restaurantes')->onDelete('cascade');
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->decimal('precio', 8, 2)->default(0);
                $table->boolean('disponible')->default(true);
                $table->timestamps();
            });
        }

        // Ordenes
        if (! Schema::hasTable('ordenes')) {
            Schema::create('ordenes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('restaurante_id')->constrained('restaurantes')->onDelete('cascade');
                $table->foreignId('repartidor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('estado', ['recibida', 'preparando', 'en_camino', 'entregada', 'cancelada'])->default('recibida');
                $table->decimal('total', 8, 2)->default(0);
                $table->string('direccion_entrega');
                $table->timestamps();
            });
        }

        // Ratings
        if (! Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('orden_id')->constrained('ordenes')->onDelete('cascade');
                $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
                $table->integer('calificacion');
                $table->text('comentario')->nullable();
                $table->timestamps();
            });
        }

        // Pivot orden_producto
        if (! Schema::hasTable('orden_producto')) {
            Schema::create('orden_producto', function (Blueprint $table) {
                $table->id();
                $table->foreignId('orden_id')->constrained('ordenes')->onDelete('cascade');
                $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
                $table->integer('cantidad')->default(1);
                $table->decimal('precio_unitario', 8, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_producto');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('ordenes');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('restaurantes');
    }
};
