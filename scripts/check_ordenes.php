<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Orden;
use App\Models\User;

echo "=== ÓRDENES EN BASE DE DATOS ===\n\n";

$ordenes = Orden::select('id', 'estado', 'repartidor_id')->get();

foreach ($ordenes as $o) {
    $rep_info = $o->repartidor_id ? User::find($o->repartidor_id)->name : "SIN ASIGNAR";
    echo "ID: {$o->id} | Estado: {$o->estado} | Repartidor: {$rep_info}\n";
}
