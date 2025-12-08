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
        // Verificar si la columna 'calificacion' existe y renombrarla a 'puntuacion'
        if (Schema::hasTable('ratings') && Schema::hasColumn('ratings', 'calificacion')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->renameColumn('calificacion', 'puntuacion');
            });
        }

        // Agregar columna repartidor_id si no existe
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
        if (Schema::hasTable('ratings')) {
            Schema::table('ratings', function (Blueprint $table) {
                if (Schema::hasColumn('ratings', 'puntuacion')) {
                    $table->renameColumn('puntuacion', 'calificacion');
                }
                if (Schema::hasColumn('ratings', 'repartidor_id')) {
                    $table->dropForeignIdFor(\App\Models\User::class, 'repartidor_id');
                    $table->dropColumn('repartidor_id');
                }
            });
        }
    }
};
