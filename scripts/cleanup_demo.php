<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Demo identifiers inserted by the seeder
$demoUserEmails = [
    'repartidor1@example.com',
    'repartidor2@example.com',
    'repartidor3@example.com',
    'restowner1@example.com',
    'restowner2@example.com',
    'restowner3@example.com',
    'cliente1@example.com',
    'cliente2@example.com',
];

echo "This script will DELETE demo data that was added by the seeder.\n";
echo "It will remove users with these emails: \n" . implode("\n", $demoUserEmails) . "\n\n";
echo "It will also remove restaurantes named 'Demo Restaurante %' and productos 'Producto Demo %', and any orders related to them.\n\n";

// Allow non-interactive confirmation by passing YES as first argument
if (isset($argv) && count($argv) > 1 && $argv[1] === 'YES') {
    $confirm = 'YES';
} else {
    fwrite(STDOUT, "Type YES to proceed: ");
    $confirm = trim(fgets(STDIN));
}

if ($confirm !== 'YES') {
    echo "Aborted. No changes made.\n";
    exit(0);
}

DB::beginTransaction();
try {
    // Find user ids to delete
    $users = DB::table('users')->whereIn('email', $demoUserEmails)->pluck('id')->toArray();

    // Find restaurantes created with Demo Restaurante % names
    $restIds = DB::table('restaurantes')->where('nombre', 'like', 'Demo Restaurante %')->pluck('id')->toArray();

    // Find productos ids
    $prodIds = DB::table('productos')->where('nombre', 'like', 'Producto Demo %')->pluck('id')->toArray();

    // Delete pivot orden_producto rows for those productos or ordenes that belong to demo restaurants/users
    if (!empty($prodIds)) {
        $deleted = DB::table('orden_producto')->whereIn('producto_id', $prodIds)->delete();
        echo "Deleted {$deleted} orden_producto rows by producto_id.\n";
    }

    // Delete ordenes that are related to demo clientes or demo restaurantes
    $ordenesDeleted = 0;
    if (!empty($users)) {
        $ordenesDeleted += DB::table('ordenes')->whereIn('cliente_id', $users)->orWhereIn('repartidor_id', $users)->delete();
    }
    if (!empty($restIds)) {
        $ordenesDeleted += DB::table('ordenes')->whereIn('restaurante_id', $restIds)->delete();
    }
    echo "Deleted {$ordenesDeleted} ordenes.\n";

    // Delete productos
    $prodDeleted = 0;
    if (!empty($prodIds)) {
        $prodDeleted = DB::table('productos')->whereIn('id', $prodIds)->delete();
    }
    echo "Deleted {$prodDeleted} productos.\n";

    // Delete restaurantes
    $restDeleted = 0;
    if (!empty($restIds)) {
        $restDeleted = DB::table('restaurantes')->whereIn('id', $restIds)->delete();
    }
    echo "Deleted {$restDeleted} restaurantes.\n";

    // Finally delete the users
    $usersDeleted = 0;
    if (!empty($users)) {
        $usersDeleted = DB::table('users')->whereIn('id', $users)->delete();
    }
    echo "Deleted {$usersDeleted} users.\n";

    DB::commit();
    echo "Cleanup completed successfully.\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "Error during cleanup: " . $e->getMessage() . "\n";
}
