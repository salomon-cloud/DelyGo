<?php
// Verificar usuarios de prueba

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== VERIFICACIÓN DE USUARIOS DE PRUEBA ===\n";
echo "=========================================\n\n";

$users = User::orderBy('id')->get(['id', 'name', 'email', 'rol', 'created_at']);

if ($users->isEmpty()) {
    echo "❌ NO HAY USUARIOS EN LA BASE DE DATOS\n";
    exit(1);
}

echo sprintf("%-3s | %-20s | %-25s | %-15s | %s\n", "ID", "Nombre", "Email", "Rol", "Creado");
echo str_repeat("-", 100) . "\n";

foreach ($users as $user) {
    echo sprintf("%-3d | %-20s | %-25s | %-15s | %s\n", 
        $user->id, 
        substr($user->name, 0, 20), 
        $user->email, 
        $user->rol,
        $user->created_at->format('Y-m-d H:i:s')
    );
}

echo "\n" . str_repeat("-", 100) . "\n";
echo "Total usuarios: " . count($users) . "\n\n";

// Verificar roles
echo "=== CONTEO POR ROL ===\n";
$roles = User::groupBy('rol')->selectRaw('rol, count(*) as total')->get();
foreach ($roles as $role) {
    echo "- {$role->rol}: {$role->total}\n";
}

echo "\n✅ Verificación completada\n";
