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
       if (! Schema::hasTable('productos')) {
           // Create the productos table and add the restaurante_id column first.
           // We add the foreign key constraint afterwards only if the restaurantes table exists
           Schema::create('productos', function (Blueprint $table) {
               $table->id();
               // create as unsignedBigInteger to avoid issues if the referenced table is not yet created
               $table->unsignedBigInteger('restaurante_id'); // A qué restaurante pertenece
               $table->string('nombre');
               $table->text('descripcion')->nullable();
               $table->decimal('precio', 8, 2);
               $table->boolean('disponible')->default(true);
               $table->timestamps();
           });

           // If restaurantes table already exists (migration order issues), add FK constraint now
           if (Schema::hasTable('restaurantes')) {
               Schema::table('productos', function (Blueprint $table) {
                   $table->foreign('restaurante_id')->references('id')->on('restaurantes')->onDelete('cascade');
               });
           }
       }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
