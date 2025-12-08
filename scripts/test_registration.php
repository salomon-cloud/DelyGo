<?php
// Crea un usuario de prueba usando la fábrica para verificar persistencia y rol.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\UserFactory;

echo "Creating test user via UserFactory...\n";

try {
    $user = UserFactory::crearUsuario('admin', [
        'name' => 'Script Test User',
        'email' => 'script-test+' . time() . '@example.local',
        'password' => bcrypt('secret-password'),
    ]);

    echo "Created user id={$user->id} email={$user->email} rol={$user->rol}\n";
} catch (Throwable $e) {
    echo "Error creating user: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "Test user creation complete.\n";
