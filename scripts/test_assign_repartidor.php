<?php
// Script para probar asignación de repartidor

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Orden;

echo "=== PRUEBA DE ASIGNACIÓN DE REPARTIDOR ===\n\n";

// 1. Obtener una orden sin repartidor
echo "[1/4] Buscando orden sin repartidor...\n";
$orden = Orden::where('repartidor_id', null)->orWhere('repartidor_id', 0)->first();

if (!$orden) {
    // Crear una orden de prueba
    echo "No hay órdenes sin repartidor. Creando una de prueba...\n";
    $cliente = User::where('rol', 'cliente')->first();
    $restaurante = User::where('rol', 'restaurante')->first()->restaurante;
    
    if (!$cliente || !$restaurante) {
        echo "❌ No hay usuarios de prueba suficientes.\n";
        exit(1);
    }
    
    $orden = Orden::create([
        'cliente_id' => $cliente->id,
        'restaurante_id' => $restaurante->id,
        'estado' => 'recibida',
        'total' => 100,
        'direccion_entrega' => 'Calle Test 123',
    ]);
}

echo "✅ Orden encontrada:\n";
echo "   ID: {$orden->id}\n";
echo "   Estado: {$orden->estado}\n";
echo "   Repartidor actual: " . ($orden->repartidor_id ? $orden->repartidor_id : "SIN ASIGNAR") . "\n\n";

// 2. Cambiar estado a "preparando"
echo "[2/4] Cambiando estado a 'preparando'...\n";
try {
    $orden->transicionarA('preparando');
    echo "✅ Estado cambiado a: {$orden->estado}\n\n";
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    exit(1);
}

// 3. Obtener un repartidor
echo "[3/4] Obteniendo un repartidor...\n";
$repartidor = User::where('rol', 'repartidor')->first();

if (!$repartidor) {
    echo "❌ No hay repartidores disponibles.\n";
    exit(1);
}

echo "✅ Repartidor encontrado: {$repartidor->name} (ID: {$repartidor->id})\n\n";

// 4. Asignar repartidor (simular lo que hace el controlador)
echo "[4/4] Asignando repartidor...\n";
$orden->repartidor_id = $repartidor->id;
$orden->save();
echo "✅ Repartidor guardado en la orden.\n";

// Validar que se guardó
$orden->refresh();
echo "\n=== RESULTADO FINAL ===\n";
echo "Orden ID: {$orden->id}\n";
echo "Estado: {$orden->estado}\n";
echo "Repartidor asignado: {$orden->repartidor_id}\n";
echo "Nombre repartidor: " . ($orden->repartidor_id ? User::find($orden->repartidor_id)->name : "NINGUNO") . "\n";

if ($orden->repartidor_id === $repartidor->id) {
    echo "\n✅ ÉXITO: El repartidor fue asignado correctamente.\n";
} else {
    echo "\n❌ FALLO: El repartidor NO fue asignado.\n";
    exit(1);
}
