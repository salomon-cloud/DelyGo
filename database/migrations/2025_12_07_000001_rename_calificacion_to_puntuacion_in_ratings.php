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
        // Si la tabla existe y tiene el campo calificacion, lo renombramos a puntuacion
        if (Schema::hasTable('ratings') && Schema::hasColumn('ratings', 'calificacion')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->renameColumn('calificacion', 'puntuacion');
            });
        }
        
        // Asegurar que repartidor_id existe (si no, lo añadimos)
        if (Schema::hasTable('ratings') && !Schema::hasColumn('ratings', 'repartidor_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->foreignId('repartidor_id')->nullable()->constrained('users')->nullOnDelete()->after('cliente_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ratings') && Schema::hasColumn('ratings', 'puntuacion')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->renameColumn('puntuacion', 'calificacion');
            });
        }
        
        if (Schema::hasTable('ratings') && Schema::hasColumn('ratings', 'repartidor_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['repartidor_id']);
                $table->dropColumn('repartidor_id');
            });
        }
    }
};
