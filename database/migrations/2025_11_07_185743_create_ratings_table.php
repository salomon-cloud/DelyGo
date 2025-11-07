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
       Schema::create('ratings', function (Blueprint $table) {
    $table->id();
    // Referencia explícita a la tabla 'ordenes'
    $table->foreignId('orden_id')->constrained('ordenes')->onDelete('cascade');
    $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
    $table->integer('calificacion')->comment('1 a 5 estrellas'); // Calificación 1-5
    $table->text('comentario')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
