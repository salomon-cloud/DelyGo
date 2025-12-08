<?php
// Crea una orden demo en estado 'preparando' para pruebas.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Orden;
use App\Models\User;
use App\Models\Restaurante;

// Encuentra un admin o usa el primero
$admin = User::where('rol', 'admin')->first();
if (! $admin) {
    echo "No admin user found. Run scripts/create_admin_demo.php first.\n";
    exit(1);
}

// Encuentra un restaurante disponible
$rest = Restaurante::first();
if (! $rest) {
    echo "No restaurante found. Run scripts/seed_asignacion_demo.php first.\n";
    exit(1);
}

// Crear la orden
$orden = Orden::create([
    'cliente_id' => $admin->id,
    'restaurante_id' => $rest->id,
    'repartidor_id' => null,
    'estado' => 'preparando',
    'total' => 50.00,
    'direccion_entrega' => 'Direccion demo 123',
]);

echo "Created demo order: id={$orden->id} restaurante_id={$rest->id} cliente_id={$admin->id}\n";
