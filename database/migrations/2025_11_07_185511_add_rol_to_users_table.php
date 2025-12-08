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
    Schema::table('users', function (Blueprint $table) {
        // 'cliente', 'restaurante', 'repartidor', 'admin'
        $table->enum('rol', ['cliente', 'restaurante', 'repartidor', 'admin'])->default('cliente')->after('name');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('rol');
    });
}
};
